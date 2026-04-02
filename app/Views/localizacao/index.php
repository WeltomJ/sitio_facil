<?php
/**
 * View de pesquisa por localização - Manaus
 * Integra APIs gratuitas: OpenStreetMap, Wikipedia, Open-Meteo
 */
?&gt;

<div class="container-localizacao"&gt;
    <header class="localizacao-header"&gt;
        <h1&gt;<i class="icon-map"&gt;</i&gt; Pesquisa por Localização</h1&gt;
        <p class="subtitulo"&gt;Descubra Manaus e encontre chácaras próximas</p&gt;
    </header&gt;

    <div class="info-cidade" id="info-cidade"&gt;
        <div class="loading"&gt;Carregando informações da cidade...</div&gt;
    </div&gt;

    <div class="clima-atual" id="clima-atual"&gt;
        <div class="loading"&gt;Carregando clima...</div&gt;
    </div&gt;

    <section class="busca-geocodificacao"&gt;
        <h2&gt;Buscar Endereço em Manaus</h2&gt;
        <form id="form-geocodificar" class="form-inline"&gt;
            <input
                type="text"
                id="endereco-busca"
                placeholder="Digite um bairro, rua ou ponto de referência..."
                required
                class="input-large"
            >
            <button type="submit" class="btn btn-primary"&gt;
                <i class="icon-search"&gt;</i> Buscar
            </button>
        </form>
        <div id="resultado-geocodificacao" class="resultado-container"></div>
    </section&gt;

    <section class="mapa-container"&gt;
        <h2&gt;Mapa de Manaus</h2&gt;
        <div class="categorias-pontos"&gt;
            <button class="btn-filtro active" data-categoria="turismo"&gt;
                <i class="icon-camera"&gt;</i> Pontos Turísticos
            </button&gt;
            <button class="btn-filtro" data-categoria="parques"&gt;
                <i class="icon-tree"&gt;</i> Parques
            </button&gt;
            <button class="btn-filtro" data-categoria="restaurantes"&gt;
                <i class="icon-food"&gt;</i> Restaurantes
            </button&gt;
            <button class="btn-filtro" data-categoria="hospitais"&gt;
                <i class="icon-hospital"&gt;</i> Hospitais
            </button&gt;
        </div&gt;

        <div id="mapa-manaus" class="mapa"&gt;</div&gt;

        <div id="pontos-lista" class="pontos-lista"&gt;
            <div class="loading"&gt;Carregando pontos de interesse...</div>
        </div>
    </section&gt;

    <section class="calculadora-distancia"&gt;
        <h2&gt;Calcular Distância</h2&gt;
        <p class="descricao"&gt;Calcule a distância entre dois pontos em Manaus</p&gt;

        <form id="form-distancia"&gt;
            <div class="coordenadas-grid"&gt;
                <div class="coord-group"&gt;
                    <h4&gt;Ponto A</h4&gt;
                    <label&gt;Latitude:</label&gt;
                    <input type="number" step="any" id="lat1" placeholder="-3.1019" required>

                    <label&gt;Longitude:</label>
                    <input type="number" step="any" id="lon1" placeholder="-60.0250" required>
                </div&gt;

                <div class="coord-group"&gt;
                    <h4&gt;Ponto B</h4&gt;
                    <label&gt;Latitude:</label&gt;
                    <input type="number" step="any" id="lat2" placeholder="-3.1200" required>

                    <label&gt;Longitude:</label>
                    <input type="number" step="any" id="lon2" placeholder="-60.0400" required>
                </div&gt;
            </div&gt;

            <button type="submit" class="btn btn-primary"&gt;Calcular Distância</button>
        </form&gt;

        <div id="resultado-distancia" class="resultado-container"></div>
    </section&gt;

</div&gt;

<!-- Leaflet CSS e JS (biblioteca de mapas gratuita) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" >
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Coordenadas de Manaus
    const manausLat = -3.1019;
    const manausLon = -60.0250;
    let mapa;
    let marcadores = [];

    // Inicializar mapa
    function initMapa() {
        mapa = L.map('mapa-manaus').setView([manausLat, manausLon], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(mapa);

        // Marcador do centro de Manaus
        L.marker([manausLat, manausLon])
            .addTo(mapa)
            .bindPopup('<b>Manaus</b><br>Centro da cidade')
            .openPopup();
    }

    // Carregar informações da cidade (Wikipedia)
    async function carregarInfoCidade() {
        try {
            const response = await fetch('/localizacao/info');
            const data = await response.json();

            const container = document.getElementById('info-cidade');
            container.innerHTML = `
                <div class="info-card">
                    <div class="info-content">
                        <h3>${data.titulo}</h3>
                        <p>${data.descricao}</p>
                        ${data.url_wikipedia ? `
                            <a href="${data.url_wikipedia}" target="_blank" class="btn-link">
                                Ler mais na Wikipedia →
                            </a>
                        ` : ''}
                    </div>
                    ${data.imagem ? `
                        <img src="${data.imagem}" alt="${data.titulo}" class="info-imagem">
                    ` : ''}
                </div>
            `;
        } catch (error) {
            console.error('Erro ao carregar info:', error);
            document.getElementById('info-cidade').innerHTML = '';
        }
    }

    // Carregar clima atual (Open-Meteo)
    async function carregarClima() {
        try {
            const response = await fetch('/localizacao/clima');
            const data = await response.json();

            const container = document.getElementById('clima-atual');
            container.innerHTML = `
                <div class="clima-card">
                    <i class="icon-sun">☀️</i>
                    <div class="clima-info">
                        <span class="temperatura">${data.temperatura}${data.unidade_temp}</span>
                        <span class="descricao">${data.descricao}</span>
                        <span class="vento">💨 ${data.velocidade_vento} ${data.unidade_vento}</span>
                    </div>
                </div>
            `;
        } catch (error) {
            console.error('Erro ao carregar clima:', error);
            document.getElementById('clima-atual').innerHTML = '';
        }
    }

    // Carregar pontos de interesse
    async function carregarPontos(categoria = 'turismo') {
        try {
            const container = document.getElementById('pontos-lista');
            container.innerHTML = '<div class="loading">Carregando pontos de interesse...</div>';

            const response = await fetch(`/localizacao/pontos/${categoria}`);
            const data = await response.json();

            // Limpar marcadores anteriores
            marcadores.forEach(m => mapa.removeLayer(m));
            marcadores = [];

            let html = '<ul class="pontos-ul">';
            data.pontos.forEach(ponto => {
                html += `
                    <li class="ponto-item" data-lat="${ponto.lat}" data-lon="${ponto.lon}">
                        <strong>${ponto.nome}</strong>
                        ${ponto.endereco ? `<br><small>${ponto.endereco}</small>` : ''}
                        <br><small class="coords">${ponto.lat.toFixed(4)}, ${ponto.lon.toFixed(4)}</small>
                    </li>
                `;

                // Adicionar marcador no mapa
                const marker = L.marker([ponto.lat, ponto.lon])
                    .addTo(mapa)
                    .bindPopup(`<b>${ponto.nome}</b>${ponto.endereco ? '<br>' + ponto.endereco : ''}`);
                marcadores.push(marker);
            });
            html += '</ul>';

            container.innerHTML = html;

            // Clique nos itens da lista centraliza no mapa
            container.querySelectorAll('.ponto-item').forEach(item => {
                item.addEventListener('click', () => {
                    const lat = parseFloat(item.dataset.lat);
                    const lon = parseFloat(item.dataset.lon);
                    mapa.setView([lat, lon], 16);
                });
            });

        } catch (error) {
            console.error('Erro ao carregar pontos:', error);
            document.getElementById('pontos-lista').innerHTML = '<p class="erro">Erro ao carregar pontos.</p>';
        }
    }

    // Geocodificar endereço
    document.getElementById('form-geocodificar').addEventListener('submit', async function(e) {
        e.preventDefault();
        const endereco = document.getElementById('endereco-busca').value;
        const container = document.getElementById('resultado-geocodificacao');

        container.innerHTML = '<div class="loading">Buscando endereço...</div>';

        try {
            const response = await fetch(`/localizacao/geocodificar/${encodeURIComponent(endereco)}`);
            const data = await response.json();

            if (data.erro) {
                container.innerHTML = `<div class="erro">${data.erro}</div>`;
                return;
            }

            container.innerHTML = `
                <div class="sucesso">
                    <p><strong>Endereço encontrado:</strong> ${data.display_name}</p>
                    <p><strong>Coordenadas:</strong> ${data.lat}, ${data.lon}</p>
                    <button class="btn btn-secondary" onclick="centralizarMapa(${data.lat}, ${data.lon})">
                        Ver no mapa
                    </button>
                </div>
            `;

            // Adicionar marcador temporário
            const marker = L.marker([data.lat, data.lon])
                .addTo(mapa)
                .bindPopup(`<b>${endereco}</b><br>${data.display_name}`)
                .openPopup();
            marcadores.push(marker);
            mapa.setView([data.lat, data.lon], 16);

        } catch (error) {
            container.innerHTML = '<div class="erro">Erro ao buscar endereço.</div>';
        }
    });

    // Calcular distância
    document.getElementById('form-distancia').addEventListener('submit', async function(e) {
        e.preventDefault();

        const dados = {
            lat1: parseFloat(document.getElementById('lat1').value),
            lon1: parseFloat(document.getElementById('lon1').value),
            lat2: parseFloat(document.getElementById('lat2').value),
            lon2: parseFloat(document.getElementById('lon2').value)
        };

        const container = document.getElementById('resultado-distancia');
        container.innerHTML = '<div class="loading">Calculando...</div>';

        try {
            const response = await fetch('/localizacao/distancia', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });
            const data = await response.json();

            container.innerHTML = `
                <div class="sucesso">
                    <p>📍 Distância: <strong>${data.distancia_km} km</strong>
                    (${data.distancia_m} metros)</p>
                </div>
            `;

            // Mostrar no mapa
            L.marker([dados.lat1, dados.lon1]).addTo(mapa).bindPopup('Ponto A');
            L.marker([dados.lat2, dados.lon2]).addTo(mapa).bindPopup('Ponto B');

            // Ajustar zoom para mostrar ambos
            const bounds = L.latLngBounds(
                [dados.lat1, dados.lon1],
                [dados.lat2, dados.lon2]
            );
            mapa.fitBounds(bounds);

        } catch (error) {
            container.innerHTML = '<div class="erro">Erro ao calcular distância.</div>';
        }
    });

    // Filtros de categoria
    document.querySelectorAll('.btn-filtro').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.btn-filtro').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            carregarPontos(btn.dataset.categoria);
        });
    });

    // Função global para centralizar mapa
    window.centralizarMapa = function(lat, lon) {
        mapa.setView([lat, lon], 16);
    };

    // Inicializar
    initMapa();
    carregarInfoCidade();
    carregarClima();
    carregarPontos('turismo');
});
</script>

<style>
.container-localizacao {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.localizacao-header {
    text-align: center;
    margin-bottom: 30px;
}

.localizacao-header h1 {
    color: var(--primary-color, #333);
    margin-bottom: 10px;
}

.subtitulo {
    color: #666;
    font-size: 1.1em;
}

/* Cards de info */
.info-cidade {
    margin-bottom: 20px;
}

.info-card {
    display: flex;
    gap: 20px;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.info-content {
    flex: 1;
}

.info-content h3 {
    margin-top: 0;
    color: #333;
}

.info-imagem {
    width: 200px;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.btn-link {
    display: inline-block;
    margin-top: 10px;
    color: #0066cc;
    text-decoration: none;
}

/* Clima */
.clima-card {
    display: flex;
    align-items: center;
    gap: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    margin-bottom: 30px;
}

.clima-card .icon-sun {
    font-size: 3em;
}

.clima-info {
    display: flex;
    flex-direction: column;
}

.temperatura {
    font-size: 2em;
    font-weight: bold;
}

/* Busca */
.busca-geocodificacao {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.form-inline {
    display: flex;
    gap: 10px;
}

.input-large {
    flex: 1;
    padding: 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    font-size: 1em;
}

/* Mapa */
.mapa-container {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.categorias-pontos {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.btn-filtro {
    padding: 10px 20px;
    border: 2px solid #ddd;
    background: white;
    border-radius: 25px;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-filtro:hover,
.btn-filtro.active {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

.mapa {
    height: 400px;
    border-radius: 12px;
    margin-bottom: 20px;
}

.pontos-lista {
    max-height: 300px;
    overflow-y: auto;
}

.pontos-ul {
    list-style: none;
    padding: 0;
}

.ponto-item {
    padding: 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s;
}

.ponto-item:hover {
    background: #f5f5f5;
}

.ponto-item .coords {
    color: #999;
}

/* Calculadora de distância */
.calculadora-distancia {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.coordenadas-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.coord-group {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.coord-group h4 {
    margin-top: 0;
    color: #667eea;
}

.coord-group label {
    display: block;
    margin: 10px 0 5px;
    font-weight: 500;
}

.coord-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-sizing: border-box;
}

/* Resultados */
.resultado-container {
    margin-top: 15px;
}

.sucesso {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 8px;
}

.erro {
    background: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 8px;
}

.loading {
    color: #666;
    font-style: italic;
}

/* Responsivo */
@media (max-width: 768px) {
    .info-card {
        flex-direction: column;
    }

    .info-imagem {
        width: 100%;
        height: 200px;
    }

    .form-inline {
        flex-direction: column;
    }

    .coordenadas-grid {
        grid-template-columns: 1fr;
    }

    .categorias-pontos {
        justify-content: center;
    }
}
</style>
