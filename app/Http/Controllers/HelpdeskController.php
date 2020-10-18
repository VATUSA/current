<?php
namespace App\Http\Controllers;

use App\Classes\EmailHelper;
use App\Facility;
use App\KnowledgebaseCategories;
use App\KnowledgebaseQuestions;
use App\Role;
use App\Ticket;
use App\TicketHistory;
use App\TicketReplies;
use App\User;
use Illuminate\Http\Request;
use App\Classes\RoleHelper;
use Auth;

class HelpDeskController
    extends Controller
{
    public function getIndex()
    {
        return view('help.index');
    }

    public function getKBIndex()
    {
        return view('help.kb');
    }

    public function getList(Request $request, $status)
    {
        if (!Auth::check()) return redirect('/')->with("error", "Must be logged in to visit ticket center");

        if ($status == "search") {
            return view('help.search');
        }
        $sort = $sortdir = $page = $perpage = $start = $pages = null;
        if ($status == "mine") {
            $tickets = Ticket::where('cid', Auth::user()->cid)->orderBy('created_at', 'asc')->get();
            $status = "My";
        } elseif ($status == "myassigned") {
            $tickets = Ticket::where('assigned_to', Auth::user()->cid)->where('status', 'Open')->orderBy('created_at', 'asc')->get();
            $status = "My Assigned";
        } elseif ($status == "open") {
            $status = "Open";
            if(RoleHelper::isVATUSAStaff())
                $tickets = Ticket::where('status', 'Open')->orderBy('created_at', 'asc')->get();
            else
                $tickets = Ticket::where('status', 'Open')->where(function($query) {
                    $query->where('facility', Auth::user()->facility)
                        ->orwhere('assigned_to', Auth::user()->cid);
                        })->get();
        } elseif ($status == "closed") {
            $status = "Closed";

            // Build query
            $tickets = new Ticket;
            $tickets = $tickets->where('status', 'Closed');
            if(!RoleHelper::isVATUSAStaff())

            if (!RoleHelper::isVATUSAStaff())
                $tickets = $tickets->where(function($query) {
                    $query->where('facility', Auth::user()->facility)
                        ->orwhere('assigned_to', Auth::user()->cid);
                });
            // Add sort row
            $sort = $request->input("sort", "created_at");
            $sortdir = $request->input("dir", "desc");
            if ($sortdir == "desc") { $osortdir = "asc"; } else { $osortdir = "desc"; }
            $page = $request->input("page", 1);
            $perpage = 20;
            $start = ($page - 1) * $perpage;
            $pages = ceil(Ticket::count() / $perpage);
            $tickets = $tickets->orderBy($sort, $sortdir)->offset($start)->limit($perpage)->get();

            return view('help.listclosed', ["tickets" => $tickets, "status" => $status, 'page' => $page, 'perpage' => $perpage, 'sort' => $sort, 'sortdir' => $sortdir, 'pages' => $pages, 'osortdir' => $osortdir]);
        } else {
            return redirect('/help')->with("error", "Unknown status given for listing of tickets");
        }

        return view('help.list', ["tickets" => $tickets, "status" => $status, 'page' => $page, 'perpage' => $perpage, 'sort' => $sort, 'sortdir' => $sortdir, 'pages' => $pages]);
    }

    public function postList($status) {
        if (!Auth::check()) return redirect('/')->with("error", "Must be logged in to visit ticket center");

        if ($status != "search") { return direct('/')->with("error", "Unknown status in post to ticket center"); }

        if (isset($_POST['cid']) && $_POST['cid']) {
            $ticket = Ticket::where('cid', $_POST['cid']);
        }
        if ($_POST['facility'] != "0") {
            if (isset($ticket)) $ticket = $ticket->where('facility', $_POST['facility']);
            else $ticket = Ticket::where('facility', $_POST['facility']);
        }
        if ($_POST['status'] != "0") {
            if (isset($ticket)) $ticket = $ticket->where('status', $_POST['status']);
            else $ticket = Ticket::where('status', $_POST['status']);
        }
        if (isset($ticket))
            $tickets = $ticket->orderBy('created_at', 'asc')->get();
        else
            return redirect('/help/ticket/search')->with("error", "No search items passed.");

        return view('help.list', ["tickets" => $tickets, "status" => "Searched"]);
    }

    public function getNew()
    {
        if (!Auth::check()) return redirect('/')->with('error', "Must be logged in to submit a ticket");

        return view('help.openticket');
    }

    public function postNew(Request $request)
    {
        if (!Auth::check()) return redirect('/')->with('error', "Must be logged in to submit a ticket");

        $this->validate($request, [
            'tSubject' => 'required|max:255',
            'tFacility' => 'required|min:3|max:3',

            'tMessage' => 'required'
        ]);

        $ticket = new Ticket();
        $ticket->cid = Auth::user()->cid;
        $ticket->subject = $request->input("tSubject");
        $ticket->body = $request->input("tMessage");
        $ticket->status = "Open";
        $ticket->facility = $request->input("tFacility");
        $ticket->assigned_to = (isset($_POST["tAssign"])?$request->input("tAssign") : 0);
        $ticket->priority = "normal";
        $ticket->save();

        $history = new TicketHistory();
        $history->ticket_id = $ticket->id;
        $history->entry = "Ticket created by " . Auth::user()->fullname() . " (" . Auth::user()->cid . ")";
        $history->save();

        $emails = [];
        $emails[] = "vatusa6@vatusa.net"; // During debug period
        $emails[] = Auth::user()->email;

        // Build emails array
        if ($ticket->assigned_to == 0) {
            // Send to all in ZHQ
            if ($ticket->facility == "ZHQ") {
                $users = Role::where('facility', 'ZHQ')->where('role', 'LIKE', "US%")->where('role','NOT LIKE','USWT')->get();
                foreach ($users as $user) {
                    $emails[] = $user->user->email;
                }
            }
            elseif ($ticket->facility == "ZAE") {
                $emails[] = "vatusa3@vatusa.net";
            }
            else {
                $fac = Facility::find($ticket->facility);
                if (!$fac) {
                    $ticket->delete();
                    return redirect('/help/ticket/new')->with("error", "Invalid facility specified");
                }
                $emails[] = $fac->id . "-atm@vatusa.net";
                $emails[] = $fac->id . "-datm@vatusa.net";
                $emails[] = $fac->id . "-ta@vatusa.net";
                $emails[] = $fac->id . "-fe@vatusa.net";
                $emails[] = $fac->id . "-ec@vatusa.net";
                $emails[] = $fac->id . "-wm@vatusa.net";
            }
        } else {
            $u = User::find($ticket->assigned_to);
            if (!$u) { $ticket->delete(); return redirect('/help/ticket/new')->with("error","Invalid assignedTo user"); }
            $emails[] = $u->email;
        }

        EmailHelper::sendSupportEmail(array_unique($emails), $ticket->id, "New Ticket", "emails.help.newticket", ["ticket" => $ticket]);

        return redirect("/help/ticket/" . $ticket->id)->with("success", "Ticket successfully submitted");
    }

    public function getTicketToggleStatus($id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return redirect('/help')->with("error","Ticket not found");

        if (!Auth::check()) return redirect('/')->with('error', 'Must be logged in');

        if ($ticket->cid == Auth::user()->cid || RoleHelper::isFacilityStaff(null, $ticket->facility) || RoleHelper::isInstructor(null, $ticket->facility)) {
            if ($ticket->status == "Open") {
                $ticket->status = "Closed";
                $history = new TicketHistory();
                $history->ticket_id = $ticket->id;
                $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") closed the ticket.";
                $history->save();
                    EmailHelper::sendSupportEmail($ticket->submitter->email, $ticket->id, "Ticket Closed", "emails.help.closed", ["ticket" => $ticket, "closer" => Auth::user()->fullname()]);
            } else {
                $ticket->status = "Open";
                $history = new TicketHistory();
                $history->ticket_id = $ticket->id;
                $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") opened the ticket.";
                $history->save();
                EmailHelper::sendSupportEmail($ticket->submitter->email, $ticket->id, "Ticket Opened", "emails.help.reopened", ["ticket" => $ticket, "closer" => Auth::user()->fullname()]);
            }
            $ticket->save();
        }
        return redirect('/help/ticket/' . $ticket->id)->with("success", "Ticket status set to \"" . $ticket->status . "\"");
    }

    public function getTicket(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return redirect('/help')->with("error","Ticket not found");

        if (!Auth::check()) return redirect('/')->with('error', 'Must be logged in');

        if ($ticket->cid == Auth::user()->cid || RoleHelper::isFacilityStaff(null, $ticket->facility) || RoleHelper::isInstructor(null, $ticket->facility)) {
            return view('help.viewticket', ['ticket' => $ticket]);
        }
        else
            return redirect('/help')->with("error", "Access to ticket denied");
    }

    public function postTicket(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        if (!$ticket) return redirect('/help')->with("error","Ticket not found");

        if (!Auth::check()) return redirect('/')->with('error', 'Must be logged in');

        if ($request->input("tReply", null) == null) { return redirect("/help/ticket/$id")->with("error","You cannot post a reply with an empty message.  If you're trying to open/close the ticket, use the toggle in the ticket summary."); }

        if ($ticket->cid == Auth::user()->cid || RoleHelper::isFacilityStaff(null, $ticket->facility) || RoleHelper::isInstructor(null, $ticket->facility))
        {
            $ticket->touch();
            $reply = new TicketReplies();
            $reply->ticket_id = $ticket->id;
            $reply->cid = Auth::user()->cid;
            $reply->body = $request->input("tReply");
            $reply->save();

            $history = new TicketHistory();
            $history->ticket_id = $ticket->id;
            $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") replied to the ticket.";
            $history->save();

            if ($request->input("replyAndCloseSubmit")) {
                $ticket->status = "Closed";
                $history = new TicketHistory();
                $history->ticket_id = $ticket->id;
                $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") closed the ticket.";
                $history->save();
                EmailHelper::sendSupportEmail($ticket->submitter->email, $ticket->id, "Ticket Closed", "emails.help.closed", ["ticket" => $ticket, "closer" => Auth::user()->fullname()]);
            } elseif ($request->input("replyAndOpenSubmit")) {
                $ticket->status = "Open";
                $history = new TicketHistory();
                $history->ticket_id = $ticket->id;
                $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") opened the ticket.";
                $history->save();
                EmailHelper::sendSupportEmail($ticket->submitter->email, $ticket->id, "Ticket Opened", "emails.help.reopened", ["ticket" => $ticket, "closer" => Auth::user()->fullname()]);
            }
            $ticket->save();

            $emails = [];
            $emails[] = "vatusa6@vatusa.net"; // During debug period
            $emails[] = Auth::user()->email;
            if (Auth::user()->cid != $ticket->cid) {
                $emails[] = $ticket->submitter->email;
            }

            // Build emails array
            if ($ticket->assigned_to == 0) {
                // Send to all in ZHQ
                if ($ticket->facility == "ZHQ") {
                    $roles = Role::where('facility', 'ZHQ')->where('role', 'LIKE', "US%")->get();
                    foreach ($roles as $role) {
                        $emails[] = $role->user->email;
                    }
                }
                elseif ($ticket->facility == "ZAE") {
                    $emails[] = "vatusa3@vatusa.net";
                }
                else {
                    $fac = Facility::find($ticket->facility);
                    if (!$fac) {
                        $ticket->delete();
                        return redirect('/help/ticket/new')->with("error", "Invalid facility specified");
                    }
                    $emails[] = $fac->id . "-atm@vatusa.net";
                    $emails[] = $fac->id . "-datm@vatusa.net";
                    $emails[] = $fac->id . "-ta@vatusa.net";
                    $emails[] = $fac->id . "-fe@vatusa.net";
                    $emails[] = $fac->id . "-ec@vatusa.net";
                    $emails[] = $fac->id . "-wm@vatusa.net";
                }
            } else {
                $u = User::find($ticket->assigned_to);
                if (!$u) { $ticket->delete(); return redirect('/help/ticket/new')->with("error","Invalid assignedTo user"); }
                $emails[] = $u->email;
            }

            EmailHelper::sendSupportEmail(array_unique($emails), $ticket->id, "New Reply", "emails.help.newreply", ["ticket" => $ticket, "reply" => $reply]);

            return redirect('/help/ticket/' . $ticket->id)->with("success", "Reply posted");
        }

        abort(403);
    }

    public function postTicketAjax(Request $request, $id)
    {
        if (!$request->ajax()) abort(403);

        $ticket = Ticket::find($id);
        if (!$ticket) abort(404);

        if (RoleHelper::isFacilityStaff(null, $ticket->facility) || RoleHelper::isInstructor(null, $ticket->facility))
        {
            if ($request->input("facility")) {
                $ticket->facility = $request->input("facility");
                $ticket->save();

                $history = new TicketHistory();
                $history->ticket_id = $ticket->id;
                $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") assigned ticket to " . $ticket->facility . " facility.";
                $history->save();
            }
            if (isset($_POST['assign'])) {
                if ($request->input("assign") == "0") {
                    $ticket->assigned_to = 0;
                    $ticket->save();

                    $history = new TicketHistory();
                    $history->ticket_id = $ticket->id;
                    $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") set ticket to unassigned.";
                    $history->save();

                    if ($ticket->facility == "ZHQ") {
                        $users = Role::where('facility', 'ZHQ')->where('role', 'LIKE', "US%");
                        foreach ($users as $user) {
                            $emails[] = $user->email;
                        }
                    }
                    elseif ($ticket->facility == "ZAE") {
                        // Find Specific ZAE Member
                        $user = Role::where('facility', 'ZHQ')
                            ->where('role', 'LIKE', "%3")
                            ->where('cid', $request->input("assign"));

                        $emails[] = $user->email;
                    }
                    else {
                        $fac = Facility::find($ticket->facility);
                        if (!$fac) {
                            $ticket->delete();
                            return redirect('/help/ticket/new')->with("error", "Invalid facility specified");
                        }
                        $emails[] = $fac->id . "-atm@vatusa.net";
                        $emails[] = $fac->id . "-datm@vatusa.net";
                        $emails[] = $fac->id . "-ta@vatusa.net";
                        $emails[] = $fac->id . "-fe@vatusa.net";
                        $emails[] = $fac->id . "-ec@vatusa.net";
                        $emails[] = $fac->id . "-wm@vatusa.net";
                        EmailHelper::sendSupportEmail(array_unique($emails), $id, "Ticket assigned to your facility", "emails.help.assigned", ["ticket" => $ticket]);
                    }
                } else {
                    $ticket->assigned_to = $request->input("assign");
                    $ticket->save();

                    $user = User::find($request->input("assign"));

                    $history = new TicketHistory();
                    $history->ticket_id = $ticket->id;
                    $history->entry = Auth::user()->fullname() . " (" . Auth::user()->cid . ") assigned the ticket to " . $user->fullname() . " (" . $user->cid . ").";
                    $history->save();

                    EmailHelper::sendSupportEmail($user->email, $id, "Ticket assigned to you", "emails.help.assigned", ["ticket" => $ticket]);
                }
            }
            if (isset($_POST['note'])) {
                $ticket->notes = $_POST['note'];
                $ticket->save();
            }
        }
        else abort(403);
    }

    // Knowledgebase Editor - Categories

    public function getKBE() {
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        return view('help.kbe.index');
    }
    public function deleteKBECategory(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $kbc = KnowledgebaseCategories::find($id);
        if (!$kbc) abort(404);
        $kbc->delete();
    }
    public function postKBECategory(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $kbc = KnowledgebaseCategories::find($id);
        if (!$kbc) abort(404);
        if (isset($_POST["name"]))
        { $kbc->name = $_POST["name"]; $kbc->save(); echo 1; return; }

        abort(503);
    }

    public function putKBECategory(Request $request) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $kbc = new KnowledgebaseCategories();
        $kbc->save();
        return $kbc->id;
    }

    // Knowledgebase Editor - Questions
    public function getKBECategory(Request $request, $id) {
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $kbc = KnowledgebaseCategories::find($id);
        if (!$kbc) return redirect('/help/kbe')->with("error", "Category not found");

        return view('help.kbe.question', ['category' => $kbc]);
    }

    public function getKBEQuestion(Request $request, $qid) {
        if (!$request->ajax()) abort(403);
        //if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $kbq = KnowledgebaseQuestions::find($qid);
        if (!$kbq) return redirect('/help/kbe/$id')->with("error", "Question not found");
        $ret['question'] = $kbq->question;
        $ret['answer'] = $kbq->answer;
        return json_encode($ret, JSON_HEX_APOS);
    }

    public function deleteKBEQuestion(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $question = KnowledgebaseQuestions::find($id);
        if (!$question) abort(404);

        $cat = $question->category;
        $question->delete();

        $o = 1;
        $qs = KnowledgebaseQuestions::where('category_id', $cat->id)->orderBy('order', 'asc')->get();
        if (!$qs) return;
        foreach($qs as $q) {
            $q->order = $o;
            $q->save();
            $o++;
        }
    }

    public function postKBEQuestionOrder(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $o = 1;
        foreach($_POST['kbe'] as $id) {
            $q = KnowledgebaseQuestions::find($id);
            $q->order = $o;
            $q->save();
            $o++;
        }
    }

    public function getKBEeditQuestion($cid, $id) {
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);
        $question = null;
        if ($id != 0) {
            $question = KnowledgebaseQuestions::find($id);
            if (!$question) return redirect('/help/kbe/' . $cid)->with("error", "Question not found.");
        }
        $cat = KnowledgebaseCategories::find($cid);
        if (!$cat) return direct('/help/kbe')->with("error", "Category not found");
        return view('help.kbe.editquestion', ['question' => $question, 'cat' => $cat]);
    }

    public function postKBEeditQuestion($cid, $id) {
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);
        $question = null;
        if ($id != 0) {
            $question = KnowledgebaseQuestions::find($id);
            if (!$question) return redirect('/help/kbe/' . $cid)->with("error", "Question not found.");
        } else {
            $question = new KnowledgebaseQuestions();
            $question->category_id = $cid;
            $oh = KnowledgebaseQuestions::where('category_id', $cid)->orderBy('order', 'DESC')->first();
            if ($oh) { $oh = $oh->order + 1; }
            else { $oh = 1; }
            $question->order = $oh;
        }
        $cat = KnowledgebaseCategories::find($cid);
        if (!$cat) return direct('/help/kbe')->with("error", "Category not found");

        $question->updated_by = Auth::user()->cid;
        $question->question = $_POST['question'];
        $question->answer = $_POST['answer'];
        $question->save();

        return redirect("/help/kbe/$cid")->with("success", "Question saved successfully.");
    }

    public function putKBEQuestion(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $vars = [];
        parse_str(file_get_contents("php://input"), $vars);
        if (!$vars['question'] || !$vars['answer']) abort(403);

        $oh = KnowledgebaseQuestions::where('category_id', $id)->orderBy('order', 'DESC')->first();
        if ($oh) { $oh = $oh->order + 1; }
        else { $oh = 1; }

        $q = new KnowledgebaseQuestions();
        $q->category_id = $id;
        $q->question = $vars['question'];
        $q->answer = $vars['answer'];
        $q->order = $oh;
        $q->updated_by = Auth::user()->cid;
        $q->save();
    }

    public function postKBEQuestion(Request $request, $id) {
        if (!$request->ajax()) abort(403);
        if (!Auth::check() || !RoleHelper::isVATUSAStaff()) abort(403);

        $q = KnowledgebaseQuestions::find($id);
        if (!$q) abort(404);

        $q->question = $_POST['question'];
        $q->answer = $_POST['answer'];
        $q->updated_by = Auth::user()->cid;
        $q->save();
    }
}
