<?php

namespace App\Http\Controllers;

use App\Classes\RoleHelper;
use App\Models\Facility;
use App\Models\TrainingBlock;
use App\Models\TrainingChapter;
use App\Models\TrainingProgress;
use Auth;
use Illuminate\Support\Facades\DB;

class CBTController
    extends Controller
{
    /********* Viewer Stuff *********/
    public function getIndex($fac = "ZAE")
    {
        $blocks = TrainingBlock::where("facility", $fac)->where("visible", 1)->orderBy("order", "ASC")->get();
        $facility = Facility::find($fac);

        return View('cbt.index', ['blocks' => $blocks, 'facname' => $facility->name, 'fac' => $fac]);
    }

    public function putIndex($chid)
    {
        if (!Auth::check()) return;
        $progress = TrainingProgress::where("cid", Auth::user()->cid)->where("chapterid", $chid)->first();
        if ($progress == null) {
            $progress = new TrainingProgress();
            $progress->cid = Auth::user()->cid;
            $progress->chapterid = $chid;
            $progress->date = \DB::raw("NOW()");
            $progress->save();
        } else {
            \DB::table("training_progress")->where("cid", Auth::user()->cid)->where("chapterid", $chid)->update(["date" => \DB::raw("NOW()")]);
        }
    }

    /******** Editor Stuff *********/
    public function getEditor($fac = null)
    {
        $this->accessCheck();

        if (($fac == null && \App\Classes\RoleHelper::isVATUSAStaff()) ||
            ($fac == null && \App\Classes\RoleHelper::isAcademyStaff())) {
            $fac = "ZAE";
        } elseif (!\App\Classes\RoleHelper::isVATUSAStaff() && $fac == null) {
            $fac = Auth::user()->facility;
        } elseif (!\App\Classes\RoleHelper::isVATUSAStaff() && !\App\Classes\RoleHelper::isAcademyStaff()) {
            $fac = Auth::user()->facility;
        }

        $this->accessCheck($fac);

        $blocks = TrainingBlock::where('facility', $fac)->orderBy("order")->get();
        $facmodel = Facility::where('id', $fac)->first();
        $facname = $facmodel->name;

        return View('cbt.editorhome', ['blocks' => $blocks, 'fac' => $fac, 'facname' => $facname]);
    }

    public function getEditorBlock($id)
    {
        $block = TrainingBlock::find($id);
        $this->accessCheck($block->facility);

        $chapters = TrainingChapter::where("blockid", $id)->orderBy("order")->get();
        $facmodel = Facility::where('id', $block->facility)->first();

        $facname = $facmodel->name;
        $fac = $facmodel->id;
        $blockname = $block->name;
        $blockid = $id;

        return View('cbt.editorchapter', ['fac' => $fac, 'facname' => $facname, 'blockname' => $blockname, 'chapters' => $chapters, 'blockid' => $blockid]);
    }

    public function ajaxBlockToggle($id)
    {
        $this->accessCheck();

        $block = TrainingBlock::where('id', $id)->first();
        if ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff() && !RoleHelper::isFacilitySeniorStaff(Auth::user()->id, $block->facility) && ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff())) {
            abort(401);
        }

        if ($block->visible == 0) {
            $block->visible = 1;
        } else {
            $block->visible = 0;
        }
        $block->save();

        echo $block->visible;
    }

    public function ajaxChangeAccess($id)
    {
        $this->accessCheck();

        $block = TrainingBlock::where('id', $id)->first();
        if ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff() && !RoleHelper::isFacilitySeniorStaff(Auth::user()->id, $block->facility) && ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff())) {
            abort(401);
        }

        if (in_array($_POST['access'], ['Senior Staff', 'Staff', 'I1', 'C1', 'S1', 'ALL'])) {
            $block->level = $_POST['access'];
        } else {
            abort(400);
        }
        $block->save();
    }

    public function ajaxDeleteBlock($id)
    {
        $this->accessCheck();

        $block = TrainingBlock::where('id', $id)->first();
        if ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff()) {
            abort(401);
        }
        if (!RoleHelper::isVATUSAStaff() && !RoleHelper::isFacilitySeniorStaff(Auth::user()->id, $block->facility) && ($block->facility == "ZAE" && !RoleHelper::isAcademyStaff())) {
            abort(401);
        }

        $fac = $block->facility; // So we can reorder.

        $block->delete();

        // Renumber the order list.
        $x = 1;
        $blocks = TrainingBlock::where('facility', $fac)->get();
        foreach ($blocks as $block) {
            $block->order = $x;
            $block->save();
            $x++;
        }
    }

    public function ajaxPutBlock($fac)
    {
        $this->accessCheck($fac);

        $facility = Facility::where('id', $fac)->first();
        if ($facility->id == "ZAE" || $facility->active == 1) {
            if (RoleHelper::isVATUSAStaff() || RoleHelper::isFacilitySeniorStaff(Auth::user()->id, $fac) || ($facility->id == "ZAE" && RoleHelper::isAcademyStaff())) {
                $highBlock = TrainingBlock::where('facility', $fac)->orderBy('order', 'DESC')->first();
                if ($highBlock) {
                    $order = $highBlock->order + 1;
                } else {
                    $order = 1;
                }
                $block = new TrainingBlock();
                $block->facility = $facility->id;
                $block->name = "New Training Block";
                $block->order = $order;
                $block->visible = 1;
                $block->save();
                echo $block->id;
            } else {
                abort(401);
            }
        } else {
            abort(401);
        }
    }

    public function ajaxRenameBlock($id)
    {
        $block = TrainingBlock::find($id);
        $this->accessCheck($block->facility);
        $block->name = $_POST['name'];
        $block->save();
        echo "1";
    }

    public function ajaxOrderBlock($fac)
    {
        $this->accessCheck();

        $x = 1;

        foreach ($_POST['cbt'] as $block) {
            $blockModel = TrainingBlock::find($block);
            $blockModel->order = $x;
            $blockModel->save();
            $x++;
        }

        echo 1;
    }

    public function ajaxChapterNew($blockid)
    {
        $block = TrainingBlock::find($blockid);
        $this->accessCheck($block->facility);

        // Since we're using CRUD and this will be a "PUT" call rather than the normal "GET"/"POST",
        // we have to pull data differently.
        $vars = array();
        parse_str(file_get_contents("php://input"), $vars);
        if (!isset($vars['name'])) {
            $vars['name'] = "New Training Chapter";
        }

        // Get Highest Order #
        $highCh = TrainingChapter::where('blockid', $blockid)->orderBy('order', 'DESC')->first();
        if ($highCh) {
            $order = $highCh->order + 1;
        } else {
            $order = 1;
        }

        $chapter = new TrainingChapter();
        $chapter->blockid = $blockid;
        $chapter->order = $order;
        $chapter->name = $vars['name'];
        $chapter->url = "";
        $chapter->visible = 1;
        $chapter->save();

        echo $chapter->id;
    }

    public function ajaxChapterModify($id)
    {
        $chapter = TrainingChapter::find($id);
        $block = TrainingBlock::find($chapter->blockid);
        $this->accessCheck($block->facility);

        if (isset($_POST['toggle'])) {
            if ($chapter->visible == 0) {
                $chapter->visible = 1;
            } else {
                $chapter->visible = 0;
            }
            $chapter->save();
            echo $chapter->visible;
            return;
        }

        if (isset($_POST['name'])) {
            $chapter->name = $_POST['name'];
            $chapter->save();
            echo 1;
            return;
        }

        if (isset($_POST['link'])) {
            if (preg_match("/\/presentation\/d\/([^\/]+)\/pub/", $_POST['link'], $matches)) {
                $url = $matches[1];
            } else {
                if (preg_match("/youtube\.com/", $_POST['link']) && !preg_match("/autoplay=1", $_POST['link'])) {
                    $url = $_POST['link'] . "?autoplay=1";
                } else {
                    $url = $_POST['link'];
                }
            }

            $chapter->url = $url;
            $chapter->save();
            echo 1;
            return;
        }

        if (isset($_POST['cbt'])) {
            $x = 1;
            foreach ($_POST['cbt'] as $ch) {
                $chapter = TrainingChapter::find($ch);
                $chapter->order = $x;
                $chapter->save();
                $x++;
            }
            echo 1;
            return;
        }
    }

    public function ajaxChapterDelete($id)
    {
        $chapter = TrainingChapter::find($id);
        $block = TrainingBlock::find($chapter->blockid);
        $this->accessCheck($block->facility);

        $blockid = $block->id;
        $chapter->delete();

        $x = 1;
        $chapters = TrainingChapter::where('blockid', $blockid)->get();
        foreach ($chapters as $chapter) {
            $chapter->order = $x;
            $chapter->save();
            $x++;
        }
    }

    public function accessCheck($fac = null)
    {
        if (!Auth::check()) abort(401);

        if (RoleHelper::isVATUSAStaff()) return true;

        if (($fac == "ZAE" && RoleHelper::isAcademyStaff()) ||
            ($fac == null && RoleHelper::isAcademyStaff())) return true;

        if (!RoleHelper::isVATUSAStaff() &&
            !RoleHelper::isFacilitySeniorStaff() &&
            !RoleHelper::isFacilitySeniorStaff(Auth::user()->id, $fac)) {

            abort(401);
        }
    }
}
