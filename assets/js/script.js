const closeWindow = document.getElementById("closeWindow")

closeWindow.addEventListener("click", (close) => {
    close.preventDefault();
    const popup = document.getElementById("popup")
    popup.classList.add("hide")
})
