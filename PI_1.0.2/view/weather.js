// ATENÇÃO: gere uma chave NOVA no OpenWeather e cole aqui (a antiga ficou exposta no zip).
const apiKey = "e84ce3c5c9398e0015aece3a7859618d";
const apiUrl = "https://api.openweathermap.org/data/2.5/weather?units=metric&lang=pt_br&q=";

const searchBox = document.querySelector(".search input");
const searchBtn = document.querySelector(".search button");
const weatherIcon = document.querySelector(".weather-icon");
const errorBox = document.querySelector(".error");
const weatherBox = document.querySelector(".weather");

// Tipo de clima da API -> arquivo de imagem
const icones = {
    Clouds: "clouds.png",
    Clear: "clear.png",
    Rain: "rain.png",
    Drizzle: "drizzle.png",
    Mist: "mist.png",
    Snow: "snow.png",
};

function mostrarErro(mensagem) {
    errorBox.querySelector("p").textContent = mensagem;
    errorBox.style.display = "block";
    weatherBox.style.display = "none";
}

async function checkWeather(city) {
    city = city.trim();
    if (!city) {
        mostrarErro("Digite o nome de uma cidade.");
        return;
    }

    try {
        const response = await fetch(apiUrl + encodeURIComponent(city) + `&appid=${apiKey}`);

        if (response.status === 404) {
            mostrarErro("Cidade não encontrada.");
            return;
        }
        if (!response.ok) {
            // 401 = chave inválida ou ainda não ativada, 429 = limite de uso, etc.
            mostrarErro(`Não foi possível consultar o clima (erro ${response.status}).`);
            return;
        }

        const data = await response.json();

        document.querySelector(".city").textContent = data.name;
        document.querySelector(".temp").textContent = Math.round(data.main.temp) + "°C";
        document.querySelector(".humidity").textContent = data.main.humidity + "%";
        // Com units=metric a API entrega o vento em m/s; x 3,6 converte para km/h
        document.querySelector(".wind").textContent = Math.round(data.wind.speed * 3.6) + " km/h";

        const arquivo = icones[data.weather[0].main];
        if (arquivo) weatherIcon.src = "../img/images/" + arquivo;

        weatherBox.style.display = "block";
        errorBox.style.display = "none";
    } catch (erro) {
        console.error("clima:", erro);
        mostrarErro("Sem conexão com o serviço de clima.");
    }
}

searchBtn.addEventListener("click", () => checkWeather(searchBox.value));

// Enter dentro do campo faz a mesma busca do clique na lupa
searchBox.addEventListener("keydown", (event) => {
    if (event.key === "Enter") checkWeather(searchBox.value);
});