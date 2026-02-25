<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamProcessRequest;
use App\Http\Resources\PlayerResource;
use App\Services\TeamSelectionService;

class TeamController extends Controller
{
    public function process(TeamProcessRequest $request, TeamSelectionService $service)
    {
        try {
            $selectedPlayers = $service->select($request->validated());
            return PlayerResource::collection($selectedPlayers);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
