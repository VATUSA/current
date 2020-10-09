<?php

use App\Classes\RoleHelper;
use App\OTSEval;
use App\OTSEvalForm;
use App\TrainingRecord;
use Faker\Factory;
use Illuminate\Database\Seeder;

class OTSEvalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ots_evals')->delete();
        DB::table('ots_evals_indicator_results')->delete();
        $records = TrainingRecord::all();
        $faker = Factory::create();
        if ($records->count()) {
            foreach ($records as $record) {
                if (in_array($record->ots_status, [1, 2])) {
                    if (!RoleHelper::isInstructor($record->instructor_id, $record->facility_id)
                        || !in_array(explode('_', $record->position)[1], ['TWR', 'APP', 'CTR'])) {
                        continue;
                    }
                    $eval = $record->otsEval()->create([
                        'training_record_id' => $record->id,
                        'student_id'         => $record->student_id,
                        'instructor_id'      => $record->instructor_id,
                        'exam_date'          => $record->session_date,
                        'facility_id'        => $record->facility_id,
                        'exam_position'      => $record->position,
                        'form_id'            => OTSEvalForm::where('position',
                            strtolower(explode('_', $record->position)[1]))->first()->id,
                        'notes'              => $faker->boolean ? $faker->paragraph() : null,
                        'result'             => $record->ots_status == 1,
                        'signature'          => 'image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj48c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iNjc0IiBoZWlnaHQ9IjEyNiI+PHBhdGggZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMDAwMDAwIiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIgZD0iTSAxIDYgYyAwLjA3IC0wLjAzIDIuNjEgLTEuNjEgNCAtMiBjIDQuNDQgLTEuMjMgOS4zNiAtMi40MiAxNCAtMyBjIDMuMjQgLTAuNCA2Ljc4IC0wLjIzIDEwIDAgYyAxLjMzIDAuMDkgMi45NSAwLjMyIDQgMSBjIDQuMTkgMi43MSA5IDYuNCAxMyAxMCBjIDIuNTkgMi4zMyA0LjkzIDUuMTIgNyA4IGMgMy45NSA1LjQ5IDcuNDggMTEuMDcgMTEgMTcgYyAyLjk2IDQuOTggNS43IDkuNzkgOCAxNSBjIDIuNzQgNi4yIDYuMTMgMTIuNzcgNyAxOSBjIDEuMzQgOS41NCAwLjI2IDIwLjU0IDAgMzEgYyAtMC4wOCAzLjA2IC0xIDkuMTYgLTEgOSBjIDAuMDIgLTEuMjMgMC43NSAtNDYuMjMgMiAtNzAgYyAwLjQ1IC04LjU1IDEuNzMgLTE2LjYzIDMgLTI1IGMgMC40MiAtMi43NCAxLjEyIC01LjU4IDIgLTggYyAwLjM4IC0xLjA2IDEuMjIgLTIuMjIgMiAtMyBjIDAuNzggLTAuNzggMS45NiAtMS43OCAzIC0yIGMgMy4xOCAtMC42OCA3LjYyIC0xLjMyIDExIC0xIGMgMy4yIDAuMyA2Ljk4IDEuNTUgMTAgMyBjIDQuNDIgMi4xMSA5LjQ0IDUuMDcgMTMgOCBjIDEuNjggMS4zOCAyLjk5IDMuOTkgNCA2IGMgMC41NyAxLjE1IDAuOTIgMi42NyAxIDQgYyAwLjI1IDQuMTkgMC40NSA5LjE1IDAgMTMgYyAtMC4xNiAxLjMzIC0wLjk4IDMuMjIgLTIgNCBjIC0zLjk5IDMuMDUgLTEwIDYuNjEgLTE1IDkgYyAtMi4zNiAxLjEzIC04LjIxIDEuOTIgLTggMiBjIDAuMzMgMC4xMyA4Ljk3IC0wLjY1IDEzIDAgYyAzLjkyIDAuNjMgOS4wMyAxLjk3IDEyIDQgYyAyLjY4IDEuODMgNS43MyA1Ljk4IDcgOSBjIDEuMTIgMi42NiAxLjM0IDcuMDcgMSAxMCBjIC0wLjI2IDIuMjIgLTEuNTkgNS4zMSAtMyA3IGMgLTEuNTkgMS45MSAtNC40NyAzLjc0IC03IDUgYyAtMTAuNjUgNS4zMiAtMjEuOSAxMC41NiAtMzMgMTUgYyAtMy43OSAxLjUxIC0xMi4yNCAyLjc2IC0xMiAzIGMgMC4yOSAwLjI5IDEwLjY5IDAuNzggMTUgMCBjIDIuMzIgLTAuNDIgNC44NiAtMi40MiA3IC00IGMgNC4xNiAtMy4wNiA4LjUzIC02LjI3IDEyIC0xMCBjIDUuMDYgLTUuNDUgOS4xMSAtMTIuNjUgMTQgLTE4IGMgMS44NCAtMi4wMiA0LjU0IC0zLjgzIDcgLTUgYyAzLjYyIC0xLjcyIDguMDYgLTMuNDYgMTIgLTQgYyA1LjI5IC0wLjczIDExLjYyIC0wLjk1IDE3IDAgYyAxMS4xMiAxLjk2IDIzLjUgNS4yIDM0IDkgYyA0LjU2IDEuNjUgOS41MiA0Ljg0IDEzIDggYyAzLjQyIDMuMTEgNi44NCA3Ljg1IDkgMTIgYyAxLjk2IDMuNzYgMy4zNCA4Ljc0IDQgMTMgYyAwLjYzIDQuMDcgLTAuMjUgOC44MSAwIDEzIGMgMC4wOCAxLjMzIDAuOTMgNC4yIDEgNCBjIDAuMzEgLTAuOTMgMi4yMiAtMTIuNzggNCAtMTkgYyAxLjU2IC01LjQ3IDMuNzEgLTEwLjk1IDYgLTE2IGMgMC45NyAtMi4xMyAyLjU2IC0zLjk4IDQgLTYgYyAyIC0yLjggMy43MSAtNS43MSA2IC04IGMgMy41OCAtMy41OCA3Ljg5IC03LjY4IDEyIC0xMCBjIDMgLTEuNyA3LjM4IC0yLjUzIDExIC0zIGMgMy44MSAtMC41IDguNDggLTAuODUgMTIgMCBjIDQuMiAxLjAxIDkuMTMgMy41MiAxMyA2IGMgNC4yMiAyLjcgMTAuOTIgOS41MSAxMiAxMCBjIDAuMzUgMC4xNiAtMC4yIC0zLjc1IC0xIC01IGMgLTEuOSAtMi45NSAtNS4zMSAtNy4wOCAtOCAtOSBjIC0xLjM2IC0wLjk3IC0zLjk4IC0wLjkxIC02IC0xIGMgLTUuNjMgLTAuMjQgLTEyLjMzIC0wLjk3IC0xNyAwIGMgLTIuMzcgMC40OSAtNC44NyAzLjEgLTcgNSBjIC00LjE5IDMuNzUgLTguNDcgNy42MiAtMTIgMTIgYyAtNi4xMSA3LjU4IC0xMS40NiAxNi43OSAtMTcgMjQgYyAtMC42OCAwLjg4IC0yLjQ2IDEuMiAtMyAyIGMgLTAuNjEgMC45MiAtMS4xNCAyLjg5IC0xIDQgYyAwLjE1IDEuMiAwLjk5IDMuMzggMiA0IGMgMi42NSAxLjYzIDcuMzggMy41NiAxMSA0IGMgNi44IDAuODIgMTUuMjEgMC42OCAyMiAwIGMgMi42NCAtMC4yNiA1LjY4IC0xLjY4IDggLTMgYyAyLjEyIC0xLjIxIDQuMyAtMy4xNSA2IC01IGMgMS44NyAtMi4wNCAzLjYgLTQuNiA1IC03IGMgMC44OCAtMS41IDEuODQgLTMuMzUgMiAtNSBjIDAuNDcgLTQuOSAwLjA0IC0xNS41NiAwIC0xNiBjIC0wLjAxIC0wLjExIC0xIDIuNzggLTEgNCBjIDAgMS4yMiAwLjM5IDIuODQgMSA0IGMgMi41OCA0LjkxIDUuODEgMTAuMzQgOSAxNSBjIDEuMDMgMS41MSAyLjUyIDIuODkgNCA0IGMgMy43OCAyLjg0IDcuOTEgNi4zMyAxMiA4IGMgNC4zNCAxLjc3IDEwLjAxIDIuNDcgMTUgMyBjIDQuMjQgMC40NSA4Ljc0IDAuMjQgMTMgMCBjIDEuNjcgLTAuMDkgMy40NSAtMC40MiA1IC0xIGMgMy42NyAtMS4zOCA3LjYxIC0zLjA5IDExIC01IGMgMS44IC0xLjAxIDMuNTEgLTIuNTEgNSAtNCBjIDIuMTUgLTIuMTUgNC42MiAtNC40NiA2IC03IGMgMi40MiAtNC40NSA0LjM1IC05Ljg4IDYgLTE1IGMgMS4zNiAtNC4yMyAyLjU1IC04LjY5IDMgLTEzIGMgMC41MyAtNS4xNyAwLjI2IC0xMC44NCAwIC0xNiBjIC0wLjA3IC0xLjMzIC0wLjM5IC0zLjA4IC0xIC00IGMgLTAuNTQgLTAuOCAtMS45NCAtMS42NSAtMyAtMiBjIC0yLjcgLTAuOSAtNi4wMSAtMS43MiAtOSAtMiBjIC0zLjg5IC0wLjM3IC04LjA3IC0wLjIzIC0xMiAwIGMgLTEuNjcgMC4xIC0zLjY4IDAuMjggLTUgMSBjIC0yLjAyIDEuMSAtNC40MiAzLjIzIC02IDUgYyAtMC45IDEuMDIgLTEuNSAyLjYxIC0yIDQgYyAtMS4xNiAzLjI1IC0yLjYyIDYuNzEgLTMgMTAgYyAtMC41OCA1LjA1IC0wLjkgMTEuMzEgMCAxNiBjIDAuNjMgMy4yOCAzLjE5IDcuMjggNSAxMCBjIDAuNTggMC44OCAyLjI5IDIuMyAzIDIgYyAyLjY4IC0xLjE1IDcuMTEgLTUuNjQgMTEgLTggYyA1LjQ5IC0zLjM0IDExLjUxIC03LjUgMTcgLTkgYyA0LjU3IC0xLjI1IDExLjE2IC0wLjYzIDE2IDAgYyAyLjMgMC4zIDUuMzggMS40OCA3IDMgYyAzLjE5IDIuOTkgNy42OCA4LjA0IDkgMTIgYyAxLjMyIDMuOTYgLTAuNzggMTEuNjkgMCAxNSBjIDAuMjMgMC45OSAyLjY4IDEuODEgNCAyIGMgMi45NSAwLjQyIDYuNzggMC4yMyAxMCAwIGMgMS4zMyAtMC4wOSAyLjggLTAuNDUgNCAtMSBjIDIuMzQgLTEuMDcgNS4xOCAtMi4zMSA3IC00IGMgMi44OCAtMi42OSA1LjU3IC02LjYgOCAtMTAgYyAwLjg1IC0xLjE5IDEuNzEgLTIuNyAyIC00IGMgMC4zMiAtMS40NiAwIC01IDAgLTUgYyAwIDAgLTAuMzQgMy40NSAwIDUgYyAwLjkzIDQuMjEgMi40MSAxMC4yMiA0IDEzIGMgMC40OCAwLjg0IDIuNzggMSA0IDEgYyAxLjIyIDAgMi44IC0wLjQ0IDQgLTEgYyAzLjY1IC0xLjcgNy4zNSAtNC4zIDExIC02IGMgMS4yIC0wLjU2IDMuNDUgLTEuNTUgNCAtMSBjIDEuMTMgMS4xMyAxLjg1IDUuNDggMyA4IGMgMC40OCAxLjA3IDEuMjIgMi4yMiAyIDMgYyAwLjc4IDAuNzggMiAxLjk1IDMgMiBjIDQuNzMgMC4yNCAxMS4zNCAtMSAxNyAtMSBjIDMuMzQgMCA3LjYgMC4yIDEwIDEgYyAwLjg2IDAuMjkgMS4xNSAyLjI4IDIgMyBjIDMuMjIgMi43MyA3LjMgNi4xNSAxMSA4IGMgMi41MSAxLjI1IDYuMDIgMS42MyA5IDIgYyAyLjI2IDAuMjggNC43IDAuMjYgNyAwIGMgMy42NyAtMC40MSA3LjMxIC0xLjEyIDExIC0yIGMgNC44IC0xLjE1IDkuNjQgLTIuMDEgMTQgLTQgYyAxOC4wOSAtOC4yNSAzNi42NiAtMTYuOTcgNTQgLTI3IGMgMTAuMjkgLTUuOTUgMjAuMTggLTEzLjM5IDI5IC0yMSBjIDUuNTggLTQuODIgMTAuMTggLTExLjAzIDE1IC0xNyBjIDMuNjYgLTQuNTMgNy4wOSAtOS4xNSAxMCAtMTQgYyAyLjA0IC0zLjQgMy41NCAtNy4yNCA1IC0xMSBjIDAuODggLTIuMjYgMS42MiAtNC43MSAyIC03IGwgMCAtNSIvPjwvc3ZnPg==',
                    ]);
                }
            }
            foreach (OTSEval::all() as $eval) {
                foreach ($eval->form->indicators as $indicator) {
                    if ($indicator->header_type == 1) {
                        continue;
                    }
                    $indicator->results()->create([
                        'eval_id' => $eval->id,
                        'result'  => $faker->boolean(70) + $faker->boolean(70) + $faker->boolean(70),
                        'comment' => $faker->boolean ? $faker->sentence : null
                    ]);
                }
            }
        }
    }
}
