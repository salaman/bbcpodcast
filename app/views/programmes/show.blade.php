<div class="page-header">
    <div class="row">
        <div class="col-md-6">
            <h1>{{{ $programme->title }}}</h1>
        </div>
        <div class="col-md-6 actions">
            <a href="{{ action('ProgrammeController@rss', $programme->id) }}" type="button" class="btn btn-primary">
                <span class="glyphicon glyphicon-download"></span> Get RSS feed
            </a>

            <a href="{{ action('ProgrammeController@refresh', ['id' => $programme->id, 'url' => Request::url()]) }}" type="button" class="btn btn-default">
                <span class="glyphicon glyphicon-refresh"></span> Update metadata
            </a>

            <a href="{{ action('ProgrammeController@fetch', ['id' => $programme->id, 'url' => Request::url()]) }}" type="button" class="btn btn-default">
                <span class="glyphicon glyphicon-refresh"></span> Fetch episodes
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="thumbnail" style="width: 300px;">
            <img src="{{ $programme->image }}">
        </div>
        <p>{{{ $programme->description }}}</p>
    </div>

</div>



@if(isset($message) && $message != '')
<div class="alert alert-info">
    <p>{{{ $message }}}</p>
</div>
@endif

<h2>Episodes</h2>

<table class="table table-striped">
    <thead>
    <tr>
        <th>Image</th>
        <th>Entry ID</th>
        <th>Media ID</th>
        <th>Title</th>
        <th>Subtitle</th>
        <th>Description</th>
        <th>Duration</th>
        <th>Explicit</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($programme->entries as $entry)
    <?php
    $status = "";
    switch ($entry->status) {
        case 1:
            $status = "success";
            break;
        case 2:
            $status = "danger";
            break;
    }
    ?>
    <tr class="{{ $status }}">
        <td><img style="height: 70px;" src="{{ $entry->image }}"></td>
        <td><code>{{{ $entry->entry_id }}}</code></td>
        <td><code>{{{ $entry->mediator_id }}}</code></td>
        <td>{{{ $entry->title }}}</td>
        <td>{{{ $entry->subtitle }}}</td>
        <td>{{{ $entry->description }}}</td>
        <td>{{{ gmdate('H:i:s', $entry->duration) }}}</td>
        <td>{{ $entry->explicit
            ? '<span class="label label-danger">Yes</span>'
            : '<span class="label label-success">No</span>' }}</td>
        <td>{{{ $entry->broadcast_at }}}</td>
    </tr>
    @endforeach
    </tbody>
</table>