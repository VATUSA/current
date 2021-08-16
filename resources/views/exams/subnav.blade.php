<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Exam Center
        </h3>
    </div>
    <div class="panel-body">
        <a href="/exam">View Your Exam Results</a><br>
        <!--<a href="{{ secure_url("/my/profile#academy") }}">View Your Academy Results</a><br>-->
        <a href="{{ secure_url('exam/0') }}">View Your Assignments</a><br>
        @if(\App\Classes\RoleHelper::isFacilitySeniorStaff() || \App\Classes\RoleHelper::isInstructor())
        <hr>
        <a href="{{ secure_url("exam/assign") }}">Assign Exam</a><br>
        <a href="{{ secure_url("exam/view") }}">View Assigned Exams</a><br>
            @if(\App\Classes\RoleHelper::isFacilitySeniorStaff())
            <a href="{{ secure_url("exam/edit") }}">Edit Exams</a>
            @endif
        @endif
    </div>
</div>
