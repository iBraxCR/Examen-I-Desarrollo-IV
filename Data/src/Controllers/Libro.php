<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Container\ContainerInterface;
use App\Models\LibroModel;
use Illuminate\Database\QueryException;

class Libro {
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function read(Request $request, Response $response, array $args): Response {
        $this->container->get('eloquent');

        if (isset($args['id'])) {
            $libro = LibroModel::find($args['id']);

            if ($libro === null) {
                return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
            }

            return $this->json($response, ['data' => $libro->toArray()], 200);
        }

        $libros = LibroModel::query()
            ->orderBy('titulo')
            ->get()
            ->toArray();

        return $this->json($response, ['data' => $libros], 200);
    }

    public function create(Request $request, Response $response, array $args): Response {
        $this->container->get('eloquent');

        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        try {
            $libro = LibroModel::create([
                'isbn' => trim((string) ($data['isbn'] ?? '')),
                'titulo' => trim((string) ($data['titulo'] ?? '')),
                'autor' => trim((string) ($data['autor'] ?? '')),
                'anio_publicacion' => (int) ($data['anio_publicacion'] ?? 0),
                'ejemplares_disponibles' => (int) ($data['ejemplares_disponibles'] ?? 0),
            ]);
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return $this->json($response, ['error' => 'El ISBN ya está registrado.'], 409);
            }

            throw $exception;
        }

        return $this->json($response, ['data' => $libro->fresh()->toArray()], 201);
    }

    public function update(Request $request, Response $response, array $args): Response {
        $this->container->get('eloquent');

        $libro = LibroModel::find($args['id']);

        if ($libro === null) {
            return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
        }

        $data = $request->getParsedBody();
        $data = is_array($data) ? $data : [];

        $libro->fill([
            'isbn' => trim((string) ($data['isbn'] ?? $libro->isbn)),
            'titulo' => trim((string) ($data['titulo'] ?? $libro->titulo)),
            'autor' => trim((string) ($data['autor'] ?? $libro->autor)),
            'anio_publicacion' => (int) ($data['anio_publicacion'] ?? $libro->anio_publicacion),
            'ejemplares_disponibles' => (int) ($data['ejemplares_disponibles'] ?? $libro->ejemplares_disponibles),
        ]);

        try {
            $libro->save();
        } catch (QueryException $exception) {
            if ((int) ($exception->errorInfo[1] ?? 0) === 1062) {
                return $this->json($response, ['error' => 'El ISBN ya está registrado.'], 409);
            }

            throw $exception;
        }

        return $this->json($response, ['data' => $libro->toArray()], 200);
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $this->container->get('eloquent');

        $libro = LibroModel::find($args['id']);

        if ($libro === null) {
            return $this->json($response, ['error' => 'Libro no encontrado.'], 404);
        }

        $libro->delete();

        return $response->withStatus(204);
    }

    private function json(Response $response, array $payload, int $status): Response {
        $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
