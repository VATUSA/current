<?php namespace App\Http\Controllers;

use App\Facility;
use App\Classes\RoleHelper;
use App\Classes\Helper;
use App\User;

class InfoController
    extends Controller
{
    public function getACE()
    {
        return view('info.ace');
    }

    public function getJoin()
    {
        return View('info.join');
    }

    public function getMembers()
    {
        return view('info.members');
    }

    public function getPolicies()
    {
        return view('info.policies');
    }

    public function ajaxFacilityInfo()
    {
        if (isset($_POST['fac'])) {
            $fac = $_POST['fac'];
            if (ctype_alpha($fac) && strlen($fac) == 3) {
                $facility = Facility::where('id', $fac)->first();
                $id = $facility->id;
                $regid = $facility->region;
                switch ($regid) {
                    case 4:
                        $region = "Western";
                        break;
                    case 5:
                        $region = "South Central";
                        break;
                    case 6:
                        $region = "Midwestern";
                        break;
                    case 7:
                        $region = "Northeastern";
                        break;
                    case 8:
                        $region = "Southeastern";
                        break;
                    default:
                        $region = "Unknown";
                        break;
                }
                echo '<h2 class="text-center">' . $facility->name . '</h2>';
                echo '<h4 class="text-center">' . "$region Region" . '</h4>';
                echo '<h4 class="text-center">' . "ATD: " . RoleHelper::getNameFromRole("US{$regid}") . " (USA$regid)" . '</h4>';
                echo '<h4>Facility Staff</h4><table class="table table-hover"><thead><tr><th>Position</th><th>Name</th><th>Email</th></tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Air Traffic Manager (ATM)</td>
                                        <td>' . RoleHelper::getNameFromRole('ATM', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-ATM@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>Deputy Air Traffic Manager (DATM)</td>
                                        <td>' . RoleHelper::getNameFromRole('DATM', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-DATM@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>Training Administrator (TA)</td>
                                        <td>' . RoleHelper::getNameFromRole('TA', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-TA@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>Events Coordinator (EC)</td>
                                        <td>' . RoleHelper::getNameFromRole('EC', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-EC@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>Facility Engineer (FE)</td>
                                        <td>' . RoleHelper::getNameFromRole('FE', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-FE@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                    <tr>
                                        <td>Webmaster (WM)</td>
                                        <td>' . RoleHelper::getNameFromRole('WM', $id, 1) . '</td>
                                        <td><a href="mailto:' . $fac . '-WM@vatusa.net"><i class="fa fa-envelope"></i></a></td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                            <h4>Facility Controllers</h4>
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>CID</th>
                                    <th>Name</th>
                                    <th>Rating</th>
                                </tr>
                                </thead>
                                <tbody>';
                foreach (User::where('facility', $id)->orderBy('rating', 'desc')->orderBy('lname',
                    'asc')->orderBy('fname', 'asc')->get() as $c) {
                    echo '<tr>
                                    <td >' . $c->cid . '</td >
                                    <td >' . $c->fname . ' ' . $c->lname . '</td >
                                    <td >' . $c->urating->short . '</td >
                                </tr >';
                }
                echo '</tbody>
                            </table>';
            }
        }
    }
}
