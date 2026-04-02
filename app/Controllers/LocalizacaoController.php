<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Controller para pesquisa baseada em localização - Manaus
 * Utiliza APIs gratuitas: OpenStreetMap (Nominatim), Wikipedia, OpenWeatherMap
 */
class LocalizacaoController extends Controller
{
    // Coordenadas de Manaus
    const MANAUS_LAT = -3.1019;
    const MANAUS_LON = -60.0250;

    /**
     * Página principal de pesquisa por localização
     */
    public function index()
    {
        $dados = [
            'cidade' => 'Manaus',
            'estado' => 'Amazonas',
            'coordenadas' => [
                'lat' => self::MANAUS_LAT,
                'lon' => self::MANAUS_LON
            ]
        ];

        $this->view('localizacao/index', $dados);
    }

    /**
     * Busca coordenadas geográficas via Nominatim (OpenStreetMap) - API gratuita
     */
    public function geocodificar($endereco)
    {
        $endereco = urlencode($endereco . ', Manaus, AM, Brasil');
        $url = "https://nominatim.openstreetmap.org/search?q={$endereco}&format=json&limit=1";

        $opts = [
            'http' => [
                'header' => 'User-Agent: ProjetoScrum/1.0 (academico)'
            ]
        ];
        $context = stream_context_create($opts);

        $resposta = @file_get_contents($url, false, $context);

        if ($resposta === false) {
            return $this->json(['erro' => 'Erro ao consultar API de geocodificação'], 500);
        }

        $dados = json_decode($resposta, true);

        if (empty($dados)) {
            return $this->json(['erro' => 'Endereço não encontrado em Manaus'], 404);
        }

        return $this->json([
            'lat' => (float) $dados[0]['lat'],
            'lon' => (float) $dados[0]['lon'],
            'display_name' => $dados[0]['display_name']
        ]);
    }

    /**
     * Busca informações sobre Manaus via Wikipedia API (gratuita)
     */
    public function infoCidade()
    {
        $url = 'https://pt.wikipedia.org/api/rest_v1/page/summary/Manaus';

        $resposta = @file_get_contents($url);

        if ($resposta === false) {
            return $this->json(['erro' => 'Erro ao consultar Wikipedia'], 500);
        }

        $dados = json_decode($resposta, true);

        return $this->json([
            'titulo' => $dados['title'] ?? 'Manaus',
            'descricao' => $dados['extract'] ?? 'Cidade localizada no coração da Amazônia',
            'imagem' => $dados['thumbnail']['source'] ?? null,
            'url_wikipedia' => $dados['content_urls']['desktop']['page'] ?? null
        ]);
    }

    /**
     * Busca clima atual de Manaus via Open-Meteo (API gratuita, não requer chave)
     */
    public function clima()
    {
        $url = sprintf(
            'https://api.open-meteo.com/v1/forecast?latitude=%s&longitude=%s&current_weather=true&timezone=America/Manaus',
            self::MANAUS_LAT,
            self::MANAUS_LON
        );

        $resposta = @file_get_contents($url);

        if ($resposta === false) {
            return $this->json(['erro' => 'Erro ao consultar API de clima'], 500);
        }

        $dados = json_decode($resposta, true);
        $clima = $dados['current_weather'] ?? null;

        // Mapeia códigos de tempo para descrições
        $descricoes = [
            0 => 'Céu limpo',
            1 => 'Principalmente limpo',
            2 => 'Parcialmente nublado',
            3 => 'Nublado',
            45 => 'Nevoeiro',
            48 => 'Nevoeiro com geada',
            51 => 'Chuvisco leve',
            53 => 'Chuvisco moderado',
            55 => 'Chuvisco intenso',
            61 => 'Chuva leve',
            63 => 'Chuva moderada',
            65 => 'Chuva forte',
            80 => 'Pancadas de chuva',
            95 => 'Tempestade',
        ];

        return $this->json([
            'temperatura' => $clima['temperature'] ?? null,
            'velocidade_vento' => $clima['windspeed'] ?? null,
            'descricao' => $descricoes[$clima['weathercode']] ?? 'Tempo variável',
            'unidade_temp' => '°C',
            'unidade_vento' => 'km/h'
        ]);
    }

    /**
     * Busca pontos de interesse em Manaus via Overpass API (OpenStreetMap)
     */
    public function pontosInteresse($categoria = null)
    {
        // Query Overpass para buscar pontos turísticos em Manaus
        $query = '[out:json][timeout:25];';
        $query .= 'area[name="Manaus"]->.searchArea;';

        $filtros = [
            'turismo' => 'node["tourism"="attraction"](area.searchArea);',
            'parques' => 'node["leisure"="park"](area.searchArea);',
            'restaurantes' => 'node["amenity"="restaurant"](area.searchArea);',
            'hospitais' => 'node["amenity"="hospital"](area.searchArea);',
            'escolas' => 'node["amenity"="school"](area.searchArea);'
        ];

        if ($categoria && isset($filtros[$categoria])) {
            $query .= $filtros[$categoria];
        } else {
            // Busca todos os pontos turísticos por padrão
            $query .= $filtros['turismo'];
        }

        $query .= 'out body;>;out skel qt;';

        $url = 'https://overpass-api.de/api/interpreter';
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                           "User-Agent: ProjetoScrum/1.0 (academico)\r\n",
                'content' => 'data=' . urlencode($query)
            ]
        ];
        $context = stream_context_create($opts);

        $resposta = @file_get_contents($url, false, $context);

        if ($resposta === false) {
            return $this->json(['erro' => 'Erro ao consultar Overpass API'], 500);
        }

        $dados = json_decode($resposta, true);
        $pontos = [];

        if (isset($dados['elements'])) {
            foreach ($dados['elements'] as $elemento) {
                if ($elemento['type'] === 'node' && isset($elemento['tags']['name'])) {
                    $pontos[] = [
                        'nome' => $elemento['tags']['name'],
                        'lat' => $elemento['lat'],
                        'lon' => $elemento['lon'],
                        'tipo' => $elemento['tags']['tourism'] ?? $elemento['tags']['leisure'] ?? 'ponto_interesse',
                        'endereco' => $elemento['tags']['addr:street'] ?? null
                    ];
                }
            }
        }

        // Limita a 20 resultados
        $pontos = array_slice($pontos, 0, 20);

        return $this->json([
            'cidade' => 'Manaus',
            'categoria' => $categoria ?? 'turismo',
            'total' => count($pontos),
            'pontos' => $pontos
        ]);
    }

    /**
     * Calcula distância entre dois pontos (fórmula de Haversine)
     */
    public function calcularDistancia()
    {
        $dados = json_decode(file_get_contents('php://input'), true);

        if (!isset($dados['lat1'], $dados['lon1'], $dados['lat2'], $dados['lon2'])) {
            return $this->json(['erro' => 'Coordenadas incompletas'], 400);
        }

        $lat1 = deg2rad($dados['lat1']);
        $lon1 = deg2rad($dados['lon1']);
        $lat2 = deg2rad($dados['lat2']);
        $lon2 = deg2rad($dados['lon2']);

        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;

        $a = sin($dlat / 2) ** 2 +
             cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Raio da Terra em km
        $raioTerra = 6371;
        $distanciaKm = $raioTerra * $c;

        return $this->json([
            'distancia_km' => round($distanciaKm, 2),
            'distancia_m' => round($distanciaKm * 1000, 2)
        ]);
    }

    /**
     * Helper para retornar resposta JSON
     */
    private function json($dados, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
