const adicionar = document.querySelectorAll(".button-add-item");

adicionar.forEach((botao) => {

    // Quando este 'botão' é clicado, executa a função
    /// Lê a etiqueta data-target do botão que foi clicado.
    const alvo = botao.dataset.target;
    // É aqui que o data-target do botão e o id do container se encontram, e é por isso que precisam ser idênticos.
    const container = document.getElementById(alvo);



    container.addEventListener("click", (event) => {
      const botaoExcluir = event.target.closest(".remove-btn");
      if (!botaoExcluir) return;
      const linha = botaoExcluir.closest(".template-item");
      linha.remove();
    });

    botao.addEventListener("click", () => {
      // Procura dentro do container uma tag <template>.
      const molde = container.querySelector("template");
      // Clona o conteúdo do molde e adiciona ao container.
      const copia = molde.content.cloneNode(true);
      // Adiciona a cópia ao container.
      container.append(copia);

          // Atualiza a unidade ao trocar o item do select
    container.addEventListener("change", (event) => {
      const select = event.target.closest(".item-select");
      if (!select) return;
      const linha = select.closest(".template-item");
      const unidade = select.selectedOptions[0]?.dataset.unidade || "—";
      linha.querySelector(".unidade-item").textContent = unidade;
    });

    // Se o container pede uma linha inicial, cria uma
    if (container.dataset.inicial) botao.click();
  })
});
    document.getElementById("imagem")?.addEventListener("change",   (e) => {
      const arquivo = e.target.files[0];
      if (arquivo) document.getElementById("preview").src = URL.createObjectURL(arquivo);
});

