function paraClicked(){

        document.getElementById("abc").setAttribute("style","color:pink")
}

function paraMouseOver(){

    document.getElementById("abc").setAttribute("style","blue")
}

document.getElementById("abc").addEventListener("click",paraClicked);
document.getElementById("abc").addEventListener("mouseover",paraMouseOver);