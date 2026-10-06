const adicionar = document.querySelectorAll(".button-add-item");

adicionar.forEach((botao) => {
  botao.addEventListener("click", () => {
    const alvo = botao.dataset.ingrediente;
    const container = document.getElementById("ingrediente");
    const molde = container.querySelector(".list-inputs-container");
    const copia = molde.content.cloneNode(true);
    container.innerHTML(copia);
    
    console.log(botao.dataset.target);
    

  })
});