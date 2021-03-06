<?php

namespace App\Http\Controllers;

use App\Managers\Person\PersonManager;
use App\Models\Director;
use App\Services\DirectorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DirectorController extends Controller
{
    private $directorService;
    /**
     * @var PersonManager
     */
    private $personManager;

    public function __construct(DirectorService $directorService, PersonManager $personManager)
    {
        $this->directorService = $directorService;
        $this->personManager = $personManager;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Director::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $director = $this->personManager->createDirector($request->all());

            return response()->json($director);
        } catch (ValidationException $e) {
            return response()->json($e->errorBag);
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Director  $director
     * @return \Illuminate\Http\Response
     */
    public function show(Director $director)
    {
        return response()->json($director->load('movies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Director  $director
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Director $director)
    {
        $actor = $this->directorService->updateDirector($request->all(), $director);

        return response()->json($actor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Director  $director
     * @return \Illuminate\Http\Response
     */
    public function destroy(Director $director)
    {
        try {
            $director->delete();
            return response()->json(['data' => null, 'message' => 'Person Deleted', 'success' => true], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['data' => null, 'message' => $e->getMessage(), 'success' => false], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
