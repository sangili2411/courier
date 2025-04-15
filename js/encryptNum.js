function caesarCipher(number, shift) {
    return generateRandomAlphabets(5) + ((number + shift) % 10000) + generateRandomAlphabets(5);
}

function generateRandomAlphabets(length) {
    const alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    let result = '';
    for (let i = 0; i < length; i++) {
        const randomIndex = Math.floor(Math.random() * alphabet.length);
        result += alphabet.charAt(randomIndex);
    }
    return result;
}