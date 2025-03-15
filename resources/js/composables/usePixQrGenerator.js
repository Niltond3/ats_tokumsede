// src/composables/usePixQrGenerator.js
import { ref } from "vue";
import QRCode from "qrcode";

/**
 * Formats a monetary value string by removing currency symbols and converting
 * the decimal separator from comma to dot.
 *
 * @param {string} amountStr - A formatted monetary string (e.g., "R$ 26,00").
 * @returns {string} - A numeric string with a dot as the decimal separator (e.g., "26.00").
 */
function formatAmount(amountStr) {
    const cleaned = amountStr.replace(/[^\d,]/g, "").replace(",", ".");
    return parseFloat(cleaned).toFixed(2);
}

/**
 * Generates a Pix payload string following the Brazilian Central Bank guidelines.
 * The payload includes mandatory fields such as:
 * - "00": Payload Format Indicator ("01")
 * - "01": Point of Initiation Method ("12" for dynamic QR Code)
 * - "26": Merchant Account Information (with "BR.GOV.BCB.PIX" and the Pix key)
 * - "52": Merchant Category Code ("0000")
 * - "53": Transaction Currency ("986" for BRL)
 * - "54": Transaction Amount (if provided, without currency symbols)
 * - "58": Country Code ("BR")
 * - "59": Merchant Name (up to 25 characters)
 * - "60": Merchant City (up to 15 characters)
 * - "62": Additional Data Field Template (with txid, "*" for dynamic)
 * - "63": CRC16 checksum computed over the payload fields.
 *
 * @param {Object} params - Parameters for payload generation.
 * @param {string} params.pixKey - The Pix key (CPF, CNPJ, email, phone, or UUID).
 * @param {string} [params.amount] - The transaction amount (numeric string without symbols).
 * @param {string} [params.merchantName="COMERCIO TESTE"] - The merchant name.
 * @param {string} [params.merchantCity="BRASILIA"] - The merchant city.
 * @param {string} [params.txid="*"] - The transaction identifier.
 * @returns {string} - The complete Pix payload.
 */
export function generatePixPayload({ pixKey, amount, merchantName = "COMERCIO TESTE", merchantCity = "BRASILIA", txid = "*" }) {
    // Helper function to format a field.
    const field = (id, value) => {
        const length = value.length.toString().padStart(2, "0");
        return `${id}${length}${value}`;
    };

    // Helper function to compute CRC16-CCITT.
    const crc16CCITT = (payload) => {
        let crc = 0xFFFF;
        for (let i = 0; i < payload.length; i++) {
            crc ^= payload.charCodeAt(i) << 8;
            for (let j = 0; j < 8; j++) {
                crc = (crc & 0x8000)
                    ? ((crc << 1) ^ 0x1021) & 0xffff
                    : (crc << 1) & 0xffff;
            }
        }
        return crc.toString(16).toUpperCase().padStart(4, "0");
    };

    const payloadFormatIndicator = field("00", "01");
    const pointOfInitiationMethod = field("01", "12");

    // Merchant Account Information (ID 26)
    const merchantAccountGUI = field("00", "BR.GOV.BCB.PIX");
    const merchantAccountKey = field("01", pixKey);
    const merchantAccountInfo = field("26", merchantAccountGUI + merchantAccountKey);

    const merchantCategoryCode = field("52", "0000");
    const transactionCurrency = field("53", "986");
    const transactionAmount = amount ? field("54", amount) : "";
    const countryCode = field("58", "BR");

    const trimmedMerchantName = merchantName.substring(0, 25);
    const merchantNameField = field("59", trimmedMerchantName);
    const trimmedMerchantCity = merchantCity.substring(0, 15);
    const merchantCityField = field("60", trimmedMerchantCity);

    const txidField = field("05", txid);
    const additionalDataFieldTemplate = field("62", txidField);

    const withoutCrc =
        payloadFormatIndicator +
        pointOfInitiationMethod +
        merchantAccountInfo +
        merchantCategoryCode +
        transactionCurrency +
        transactionAmount +
        countryCode +
        merchantNameField +
        merchantCityField +
        additionalDataFieldTemplate;

    const crc = crc16CCITT(`${withoutCrc}6304`);
    const crcField = field("63", crc);

    return withoutCrc + crcField;
}

/**
 * Converts a Data URL to a Blob.
 *
 * @param {string} dataUrl - The Data URL to convert.
 * @returns {Blob} - The resulting Blob.
 */
function dataURLToBlob(dataUrl) {
    const parts = dataUrl.split(",");
    const mime = parts[0].match(/:(.*?);/)[1];
    const bstr = atob(parts[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}

/**
 * Composable that generates a Pix QR Code.
 *
 * This composable encapsulates all Pix payload logic, including
 * proper formatting of the transaction amount, payload construction,
 * and generation of both SVG and PNG QR Code images.
 *
 * @param {string} pixKey - The Pix key.
 * @param {Object} options - Additional options.
 * @param {string} options.amount - The formatted amount (e.g., "R$ 26,00").
 * @param {string} [options.merchantName] - The merchant name.
 * @param {string} [options.merchantCity] - The merchant city.
 * @param {number} [options.width=250] - The desired QR Code width in pixels.
 * @param {string} [options.txid] - The transaction identifier.
 * @returns {Object} - An object containing:
 *   - svgCode: reactive reference to the generated SVG string.
 *   - loading: reactive reference to the loading state.
 *   - error: reactive reference to any error encountered.
 *   - generatePixQr: function to generate the QR Code in SVG.
 *   - getPreparedQrCodePng: function to generate the QR Code in PNG and return a Blob.
 */
export function usePixQrGenerator(pixKey, options = {}) {
    const svgCode = ref("");
    const loading = ref(false);
    const error = ref(null);

    /**
     * Generates the Pix QR Code in SVG format.
     *
     * @returns {Promise<string>} - A promise that resolves to the SVG string.
     */
    const generatePixQr = async () => {
        loading.value = true;
        error.value = null;
        try {
            // Format the amount (e.g., "R$ 26,00" to "26.00")
            const formattedAmount = options.amount ? formatAmount(options.amount) : undefined;
            const payload = generatePixPayload({
                pixKey,
                amount: formattedAmount,
                merchantName: options.merchantName,
                merchantCity: options.merchantCity,
                txid: options.txid,
            });
            svgCode.value = await QRCode.toString(payload, { type: "svg", width: options.width || 250 });
            return svgCode.value;
        } catch (err) {
            error.value = err;
            throw err;
        } finally {
            loading.value = false;
        }
    };

    /**
     * Generates the Pix QR Code in PNG format and returns its Blob.
     *
     * @returns {Promise<Blob>} - A promise that resolves to the PNG Blob.
     */
    const getPreparedQrCodePng = async () => {
        const formattedAmount = options.amount ? formatAmount(options.amount) : undefined;
        const payload = generatePixPayload({
            pixKey,
            amount: formattedAmount,
            merchantName: options.merchantName,
            merchantCity: options.merchantCity,
            txid: options.txid,
        });
        const dataUrl = await QRCode.toDataURL(payload, { type: "image/png", width: options.width || 250 });
        return dataURLToBlob(dataUrl);
    };

    return {
        svgCode,
        loading,
        error,
        generatePixQr,
        getPreparedQrCodePng,
    };
}
