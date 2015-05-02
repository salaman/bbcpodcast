<?php

class EntryController extends \BaseController {

    protected $layout = 'layouts.master';

	/**
	 * Display a listing of entries
	 *
	 * @return Response
	 */
	public function index()
	{
		$entries = Entry::orderBy('id')->get();

        $this->layout->nav = "entries";
		$this->layout->content = View::make('entries.index', compact('entries'));
	}

	/**
	 * Show the form for creating a new entry
	 *
	 * @return Response
	 */
	public function create()
	{
		return View::make('entries.create');
	}

	/**
	 * Store a newly created entry in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validator = Validator::make($data = Input::all(), Entry::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		Entry::create($data);

		return Redirect::route('entries.index');
	}

	/**
	 * Display the specified entry.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$entry = Entry::findOrFail($id);

		return View::make('entries.show', compact('entry'));
	}

	/**
	 * Show the form for editing the specified entry.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$entry = Entry::find($id);

		return View::make('entries.edit', compact('entry'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$entry = Entry::findOrFail($id);

		$validator = Validator::make($data = Input::all(), Entry::$rules);

		if ($validator->fails())
		{
			return Redirect::back()->withErrors($validator)->withInput();
		}

		$entry->update($data);

		return Redirect::route('entries.index');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		Entry::destroy($id);

		return Redirect::route('entries.index');
	}

}