<div class="page-header">
    <h1>Entries</h1>
</div>

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
        <th>Status</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($entries as $entry)
    <tr>
        <td><img style="width: 50px;" src="{{ $entry->image }}"></td>
        <td><code>{{{ $entry->entry_id }}}</code></td>
        <td><code>{{{ $entry->mediator_id }}}</code></td>
        <td>{{{ $entry->title }}}</td>
        <td>{{{ $entry->subtitle }}}</td>
        <td>{{{ $entry->description }}}</td>
        <td>{{{ $entry->duration }}}</td>
        <td>{{{ $entry->status }}}</td>
        <td>{{{ $entry->broadcast_at }}}</td>
    </tr>
    @endforeach
    </tbody>
</table>