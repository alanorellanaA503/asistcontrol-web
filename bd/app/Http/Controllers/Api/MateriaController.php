<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MateriaRequest;
use App\Http\Resources\MateriaResource;
use App\Models\Materia;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MateriaController extends Controller
{
    public function index()
    {
        return MateriaResource::collection(Materia::orderBy('id_materia')->paginate(15));
    }

    public function store(MateriaRequest $request): JsonResponse
    {
        $materia = DB::transaction(function () use ($request): Materia {
            $codigos = Materia::query()
                ->whereRaw("codigo REGEXP '^[0-9]+$'")
                ->lockForUpdate()
                ->pluck('codigo');
            $siguiente = $codigos->map(fn (string $codigo): int => (int) $codigo)->max() + 1;

            return Materia::create([
                'codigo' => str_pad((string) $siguiente, 2, '0', STR_PAD_LEFT),
                'nombre' => $request->validated('nombre'),
            ]);
        });

        return (new MateriaResource($materia))->response()->setStatusCode(201);
    }

    public function show(Materia $materia): MateriaResource
    {
        return new MateriaResource($materia);
    }

    public function update(MateriaRequest $request, Materia $materia): MateriaResource
    {
        $materia->update($request->validated());

        return new MateriaResource($materia->refresh());
    }

    public function destroy(Materia $materia): JsonResponse
    {
        $materia->delete();

        return response()->json(['message' => 'Materia eliminada correctamente.']);
    }
}
