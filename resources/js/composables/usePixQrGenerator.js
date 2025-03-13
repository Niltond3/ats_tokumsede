import { ref } from 'vue';
import QRCode from 'qrcode';

export function usePixQrGenerator() {
    const qrCode = ref(null);
    const error = ref(null);

    const generateQR = async (pixKey, amount = null, description = null) => {
        try {
            const payload = buildPixPayload(pixKey, amount, description);
            const qr = await QRCode.toDataURL(payload);
            qrCode.value = qr;
            return qr;
        } catch (err) {
            error.value = err;
            return null;
        }
    };

    return {
        qrCode,
        error,
        generateQR
    };
}

/**
 * Builds a PIX payload following Brazilian Central Bank's BR Code specification
 * @param {string} pixKey - The PIX key
 * @param {number|null} amount - Optional transaction amount
 * @param {string|null} description - Optional transaction description
 * @returns {string} The formatted PIX payload for QR code generation
 */
const buildPixPayload = (pixKey, amount = null, description = null) => {
    const payload = {
        // Payload Format Indicator (fixed)
        '00': '01',
        // Point of Initiation Method (dynamic)
        '01': '12',
        // Merchant Account Information - PIX
        '26': {
            '00': 'br.gov.bcb.pix',
            '01': pixKey,
            ...(description && { '02': description.substring(0, 25) })
        },
        // Merchant Category Code (fixed for PIX)
        '52': '0000',
        // Transaction Currency (986 = BRL)
        '53': '986',
        // Transaction Amount (if provided)
        ...(amount && { '54': amount.toFixed(2) }),
        // Country Code (BR)
        '58': 'BR',
        // Merchant Name (using key as fallback)
        '59': 'MERCHANT_NAME',
        // Merchant City
        '60': 'CITY_NAME',
        // Additional Data Field Template
        '62': {
            '05': 'PAYMENT_REFERENCE'
        }
    };

    return formatEmv(payload);
};

/**
 * Formats the payload following EMV specification
 * @private
 * @param {Object} data - The payload object
 * @returns {string} EMV formatted string
 */
const formatEmv = (data) => {
    // Implementation of EMV formatting logic here
    // This is a simplified version - actual implementation would need
    // to handle nested objects and CRC calculation
    return Object.entries(data)
        .map(([id, value]) => `${id}${String(value).length.toString().padStart(2, '0')}${value}`)
        .join('');
};
