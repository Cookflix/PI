const adicionar = document.querySelectorAll(".button-add-item");

adicionar.forEach((botao) => {

  // Quando este 'botão' é clicado, executa a função
  botao.addEventListener("click", () => {
    /// Lê a etiqueta data-target do botão que foi clicado.
    const alvo = botao.dataset.target;
    // É aqui que o data-target do botão e o id do container se encontram, e é por isso que precisam ser idênticos.
    const container = document.getElementById(alvo);
    // Procura dentro do container uma tag <template>.
    const molde = container.querySelector("template");
    // Clona o conteúdo do molde e adiciona ao container.
    const copia = molde.content.cloneNode(true);
    // Adiciona a cópia ao container.
    container.append(copia);

  })
});
const lista = document.getElementById("ingrediente");   // o container de ingredientes

lista.addEventListener("click", (event) => {
  const botao = event.target.closest(".remove-btn");
  console.log(botao);
  if (!botao) return;
  const linha = botao.closest(".template-item");
  linha.remove();

});
