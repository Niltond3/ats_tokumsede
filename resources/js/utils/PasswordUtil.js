export default {
    generatePassword: (options) => {
        const groups = options?.groups ?? ["ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz", "1234567890"];
        const length = options?.length ?? 8;
        // Gera um caractere aleatório para cada grupo inicialmente
        let pass = groups.map((group) => group.charAt(Math.floor(Math.random() * group.length))).join("");
        const str = groups.join("");
        for (let i = pass.length; i < length; i++) {
            pass += str.charAt(Math.floor(Math.random() * str.length));
        }
        // Embaralha usando o algoritmo Fisher-Yates
        const shuffle = (array) => {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        };
        return shuffle(pass.split("")).join("");
    },
};
