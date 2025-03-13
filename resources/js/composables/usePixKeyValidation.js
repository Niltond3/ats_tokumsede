/**
 * Composable for PIX key validation following Brazilian Central Bank standards
 * @returns {Object} Validation utilities for PIX keys
 */
export function usePixKeyValidation() {
    /**
     * Validates and identifies PIX key format
     * @param {string} value - The PIX key to validate
     * @returns {string|null} The type of PIX key ('cpf', 'cnpj', 'email', 'phone', 'uuid', 'random') or null if invalid
     */
    const validatePixKeyFormat = (value) => {
        if (!value) return null;

        const patterns = {
            uuid: /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/,
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            cpf: /^\d{11}$/,
            cnpj: /^\d{14}$/,
            phone: /^\+55\d{10,11}$/,
            random: /^[a-zA-Z0-9-._@]{8,140}$/
        };

        const cleanValue = value.replace(/[^\w@.-]/g, '');

        if (patterns.uuid.test(value)) return 'uuid';
        if (patterns.email.test(value)) return 'email';
        if (patterns.phone.test(value)) return 'phone';
        if (patterns.cpf.test(cleanValue)) return 'cpf';
        if (patterns.cnpj.test(cleanValue)) return 'cnpj';
        if (patterns.random.test(value)) return 'random';

        return null;
    };

    return {
        validatePixKeyFormat
    };
}
