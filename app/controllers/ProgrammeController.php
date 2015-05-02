<?php

use Rhubarb\Exception\TimeoutException;

class ProgrammeController extends \BaseController {

    protected $layout = 'layouts.master';

    /**
     * Display a listing of programmes
     *
     * @return Response
     */
    public function index()
    {
        $programmes = Programme::orderBy('id')->get();

        $this->layout->nav = "programmes";
        $this->layout->content = View::make('programmes.index', compact('programmes'));
    }

    /**
     * Display the specified programme.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $programme = Programme::findOrFail($id);

        $this->layout->nav = "programmes";
        $this->layout->content = View::make('programmes.show', compact('programme'))->with('message', Session::get('message', ''));
    }

    public function refresh($id)
    {
        /**
         * @var $rhubarb \Rhubarb\Rhubarb
         */
        $rhubarb = App::make('rhubarb');

        $result = null;

        try {
            $task = $rhubarb->sendTask('bbcpodcast.tasks.update_programme_metadata', array($id));
            $task->delay();
            $result = $task->get();
        } catch (TimeoutException $e) {
            Log::error('task failed to return in default timelimit [10] seconds');
        }

        return Redirect::to(Input::get('url', 'programmes'))->with('message', "Successfully refreshed: " . json_encode($result, JSON_PRETTY_PRINT));
    }

    public function fetch($id)
    {
        /**
         * @var $rhubarb \Rhubarb\Rhubarb
         */
        $rhubarb = App::make('rhubarb');

        $result = null;

        try {
            $task = $rhubarb->sendTask('bbcpodcast.tasks.update_programme', array($id));
            $task->delay();
            $result = $task->get();
        } catch (TimeoutException $e) {
            Log::error('task failed to return in default timelimit [10] seconds');
        }

        return Redirect::to(Input::get('url', 'programmes'))->with('message', "Successfully fetched: " . json_encode($result, JSON_PRETTY_PRINT));
    }

    public function rss($id)
    {
        $programme = Programme::findOrFail($id);

        $view = View::make('feed')->with('programme', $programme);
        $response = Response::make($view);
        $response->header("Content-Type", "application/rss+xml");

        return $response;
    }

}