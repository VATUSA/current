@extends('layout')
@section('title', 'News Posts')

@section('scripts')
@endsection

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="row">
                <div class="col-md-offset-4 col-md-4">
                    @if ($page > 1)
                        <a href="/news/page/{{ $page - 1 }}">Previous Page</a>
                    @endif
                    Page {{ $page }}
                    <a href="/news/page/{{ $page + 1 }}">Next Page</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped">
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Date</th>
                        </tr>
                        @foreach($posts as $post)
                            <tr>
                                <td>
                                    <a href="/news/post/{{ $post['id'] }}">
                                        {{ $post['title'] }}
                                    </a>
                                </td>
                                <td>{{ $post['author_name'] }}</td>
                                <td>{{ $post['post_date'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection