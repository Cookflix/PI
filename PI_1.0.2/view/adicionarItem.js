// Seleciona os elementos principais da tela
const btnAdd = document.querySelector("#btn-add-ingrediente");
const container = document.querySelector("#container-ingredientes");

// O HTML padrão de uma nova linha. Note que ele puxa a variável 'opcoesIngredientes' que criamos no PHP.
const novaLinhaHTML = `
  <div class="linha-ingrediente" style="display: flex; gap: 10px; margin-bottom: 10px;">
    <select name="id_ingrediente[]" required>
      ${opcoesIngredientes}
    </select>
    <input type="number" name="quantidade_ingrediente[]" step="0.001" placeholder="Qtd" required />
    <button type="button" class="btn-remover-linha">X</button>
  </div>
`;

// Ação de ADICIONAR linha
btnAdd.addEventListener("click", () => {
  // beforeend insere o código no final do container, sem apagar o que já foi digitado
  container.insertAdjacentHTML('beforeend', novaLinhaHTML);
});

// Ação de REMOVER linha (Delegação de Eventos)
container.addEventListener('click', function(evento) {
    // Verifica se onde clicamos tem a classe de remover
    if (evento.target.classList.contains('btn-remover-linha')) {
        
        // Encontra a div pai inteira daquela linha específica
        const linhaParaApagar = evento.target.closest('.linha-ingrediente');
        
        // Apaga a linha inteira da tela
        if (linhaParaApagar) {
            linhaParaApagar.remove();
        }
    }
});