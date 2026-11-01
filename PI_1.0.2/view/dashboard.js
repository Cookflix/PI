const statusEl = document.getElementById("status-receitas");
const corpo = document.getElementById("corpo-receitas");
const URL_PRODUTOS = "http://localhost:3000/api/ingredientes";

// 1) a função da conta
function calcularCustoProdutos(itens, mapa) {
    let total = 0;
    // Guarda os produtos que não foram encontrados no mapa (API)
    const faltando = [];

    itens.forEach((item) => {
        const produto = mapa[item.id_produto];
        if (!produto) {
            faltando.push(item.id_produto);
            return;
        }
        total += item.quantidade * produto.preco / produto.quantidade;
    });
    return { total, faltando };
}

// 2) formatar em reais
function formatarReais(valor) {
    return valor.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

// 3) buscar os produtos e devolver o mapa (ou null se a API falhar)
function buscarProdutos() {
    return fetch(URL_PRODUTOS)
        .then((resposta) => {
            if (!resposta.ok) throw new Error("API de produtos respondeu " + resposta.status);
            return resposta.json();
        })
        .then((produtos) => {
            //objeto vazio que será usado como mapa
            const mapa = {};
            produtos.forEach((produto) => {
                mapa[produto._id] = produto;
            });
            return mapa;
        })
        .catch((erro) => {
            console.error(erro);
            return null;      // null = não consegui os produtos
        });
}
// 4) desenhar as linhas: forEach de antes, agora numa função
function desenharLinhas(receitas, mapa) {
    receitas.slice(0, 5).forEach((receita) => {
        const tr = document.createElement("tr"); // criar a linha

        const tdNome = document.createElement("td"); // cria um célula
        tdNome.textContent = receita.nome;
        tr.appendChild(tdNome); //adiciona a célula na linha

        /*const tdCusto = document.createElement("td");
        tdCusto.textContent = formatarReais(receita.custo_ingredientes);
        tr.appendChild(tdCusto);*/

        const tdRendimento = document.createElement("td");
        tdRendimento.textContent = receita.rendimento;
        tr.appendChild(tdRendimento);

        const tdTotal = document.createElement("td");

        if (receita.produtos.length === 0) {
            // nenhum produto do Mongo = o total não depende da API
            tdTotal.textContent = formatarReais(receita.custo_ingredientes);
        } else if (mapa === null) {
            tdTotal.textContent = "Total não calculado (API fora do ar)";
        } else {
            const produtos = calcularCustoProdutos(receita.produtos, mapa);

            if (produtos.faltando.length > 0) {
                tdTotal.textContent = "Produto não encontrado: total incompleto";
            } else {
                tdTotal.textContent = formatarReais(receita.custo_ingredientes + produtos.total);
            }
        }

        tr.appendChild(tdTotal);

        corpo.appendChild(tr);
    });
}

// 5) o fluxo principal
fetch("api-dashboard.php")
    .then((resposta) => { //trata a resposta HTTP
        if (!resposta.ok) throw new Error("falha");
        return resposta.json();
    })
    .then((dados) => {
        const receitas = dados.receitas;
        if (receitas.length === 0) { //verifica se a lista está vazia
            statusEl.textContent = "Você ainda não tem receitas salvas.";
            return;
        }
        return buscarProdutos().then((mapa) => desenharLinhas(receitas, mapa));
    })
    .catch((erro) => {
        console.error(erro);
        statusEl.textContent = "Não foi possível carregar suas receitas.";
    });