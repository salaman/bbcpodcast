<div class="page-header">
    <h1>Tracked Programmes</h1>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Image</th>
            <th>Playlist ID</th>
            <th>Show name</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($programmes as $programme)
        <tr>
            <td><img style="height: 100px;" src="{{ $programme->image }}"></td>
            <td><code>{{{ $programme->programme_id }}}</code></td>
            <td><a href="{{ action('ProgrammeController@show', $programme->id) }}">{{{ $programme->title }}}</a></td>
            <td>{{{ $programme->description }}}</td>
        </tr>
        @endforeach
    </tbody>
</table>