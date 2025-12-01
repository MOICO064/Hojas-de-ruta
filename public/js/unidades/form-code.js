// Solo letras
function onlyLetters(input) {
    input.value = input.value.replace(/[^A-Z\s]/gi, '').toUpperCase();
}

// Solo números
function onlyNumbers(input) {
    input.value = input.value.replace(/[^0-9]/g, '');
}

// Generar código a partir del nombre
function generateCodigo(nombre) {
    const omitWords = ['DE', 'DEL', 'LA', 'LAS', 'Y', 'EN'];
    let palabras = nombre.split(' ').filter(w => w && !omitWords.includes(w.toUpperCase()));
    let codigo = palabras.map(w => w[0].toUpperCase()).join('');
    document.getElementById('codigo').value = codigo;
}

// Si hay valor inicial al cargar la página, generar el código
document.addEventListener('DOMContentLoaded', function () {
    let nombreInput = document.getElementById('nombre');
    if (nombreInput.value) {
        generateCodigo(nombreInput.value);
    }
});
