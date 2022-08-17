<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreResume;
use App\Models\Publish;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class ResumeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $resumes = auth()->user()->resumes;

        return view('resumes.index', compact('resumes'));
    }

    public function create()
    {
        $resume = json_encode(Resume::factory()->make());
        return view('resumes.create', compact('resume'));
        /* return view('resumes.create');  */
    }

    private function savePicture($blob)
    {
        $img = Image::make($blob);
        $fileName = Str::uuid() . '.' . explode('/', $img->mime())[1];
        $filePath = "/storage/pictures/$fileName";
        $img->save(public_path($filePath));
        return $filePath;
    }

    public function store(StoreResume $request)
    {
        $data = $request->validated();
        $picture = $data['content']['basics']['picture'];
        if ($picture !== '/storage/pictures/default.png') {
            $uri = $this->savePicture($picture);
            $data['content']['basics']['picture'] = $uri;
        }

        $resume = auth()->user()->resumes()->create($data);

        Session::flash('alert', [
            'type' => 'primary',
            'messages' => ["Resume $resume->title created successfully"],
        ]);
        return response($resume, Response::HTTP_CREATED);
    }

    public function edit(Resume $resume)
    {
        $this->authorize('update', $resume);
        dd($resume->content);
        return view('resumes.edit', ['resume' => json_encode($resume)]);
    }

    public function update(StoreResume $request, Resume $resume)
    {
        $this->authorize('update', $resume);

        $data = $request->validated();
        $picture = $data['content']['basics']['picture'];
        if ($picture !== $resume->content['basics']['picture']) {
            $uri = $this->savePicture($picture);
            $data['content']['basics']['picture'] = $uri;
        }

        $resume->update($data);

        Session::flash('alert', [
            'type' => 'info',
            'messages' => ["Resume $resume->title modified successfully"],
        ]);

        return response(status: Response::HTTP_OK);
    }

    public function destroy(Resume $resume)
    {
        $this->authorize('delete', $resume);

        try {
            $resume->delete();
        } catch (QueryException $e) {
            $publish = Publish::where('resume_id', $resume->id)->first();
            return redirect()->route('resumes.index')->with('alert', [
                'type' => 'danger',
                'messages' => ["
                    Resume $resume->title cannot be deleted because publish
                    <a href='$publish->url'>$publish->url</a>
                    is using it! Delete the publish first.
                "]
            ]);
        }

        return redirect()->route('resumes.index')->with('alert', [
            'type' => 'danger',
            'messages' => ["Resume $resume->title deleted successfully"],
        ]);
    }
}
