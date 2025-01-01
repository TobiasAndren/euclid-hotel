const submitButton = document.getElementById("bookButton")
const closeWindow = document.getElementById("closeWindow")

submitButton.addEventListener("submit", (submit) =>{
    submit.preventDefault();
})

closeWindow.addEventListener("click", (close) => {
    close.preventDefault();
    const popup = document.getElementById("popup")
    popup.classList.add("hide")
})