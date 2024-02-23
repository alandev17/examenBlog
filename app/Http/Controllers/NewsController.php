<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function fetchNewsData(Request $request)
    {
        if ($request->ajax()) {
            Log::info('Iniciando solicitud AJAX para obtener datos de noticias.');

            $apiKey = env('NEWS_API_KEY');
            $pageSize = $request->length;
            $start = $request->start;
            $searchValue = $request->search['value'] ?? 'tesla';

            $currentPage = ($start / $pageSize) + 1;

            $client = new Client();

            try {
                Log::info('Realizando solicitud a la API de NewsAPI.', ['query' => $searchValue]);

                $response = $client->request('GET', 'https://newsapi.org/v2/everything', [
                    'query' => [
                        'q' => $searchValue,
                        'pageSize' => $pageSize,
                        'page' => $currentPage,
                        'apiKey' => $apiKey
                    ]
                ]);

                $body = json_decode($response->getBody()->getContents(), true);
                $articles = $body['articles'] ?? [];
                $totalResults = $body['totalResults'] ?? 0;

                // Obtiene autores aleatorios de la API de Random User
                $authorsResponse = $client->request('GET', 'https://randomuser.me/api/', [
                    'query' => [
                        'results' => $pageSize, // Obtiene la misma cantidad de autores como artículos por página
                    ]
                ]);
                $authorsBody = json_decode($authorsResponse->getBody()->getContents(), true);
                $authors = $authorsBody['results'] ?? [];

                $data = array_map(function ($article, $index) use ($authors) {
                    return [
                        'image' => $article['urlToImage'],
                        'title' => $article['title'],
                        'author' => $authors[$index]['name']['first'] . ' ' . $authors[$index]['name']['last'],
                        'authorPicture' => $authors[$index]['picture']['thumbnail'],
                        'description' => $article['description'],
                        'publishedAt' => $article['publishedAt'],
                        'url' => $article['url'],
                    ];
                }, $articles, array_keys($articles));

                Log::info('Datos obtenidos con éxito de la API.', ['totalResults' => $totalResults, 'currentPage' => $currentPage]);

                return response()->json([
                    'draw' => intval($request->draw),
                    'recordsTotal' => $totalResults,
                    'recordsFiltered' => $totalResults,
                    'data' => $data,
                ]);
            } catch (\Exception $e) {
                Log::error('Error al obtener datos de la API.', ['message' => $e->getMessage()]);

                return response()->json([
                    'error' => 'Error al obtener datos de la API.'
                ], 500);
            }
        }
    }


}
