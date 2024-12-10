import { ref } from 'vue'

const isConnected = ref(false)
const error = ref(null)
const device = ref(null)
const MAX_CHUNK_SIZE = 300; // Tamanho máximo permitido por pacote

const connectPrinter = async () => {
    // Request Bluetooth device with printer service
    device.value = await navigator.bluetooth.requestDevice({
        filters: [
            { services: ['000018f0-0000-1000-8000-00805f9b34fb'] } // Common printer service UUID
        ],
        optionalServices: ['battery_service']
    })

    // Connect to GATT Server
    const server = await device.value.gatt.connect()

    // Get printer service
    const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb')

    // Get characteristic for writing
    const characteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb')

    isConnected.value = true
    error.value = null

    // Store characteristic for printing
    window.printerCharacteristic = characteristic


}

const printData = async (data) => {
    if (!window.printerCharacteristic) {
        throw new Error('Impressora não conectada')
    }

    const encoder = new TextEncoder();
    const dataArray = encoder.encode(data);

    let offset = 0;

    while (offset < dataArray.length) {
        const chunk = dataArray.slice(offset, offset + MAX_CHUNK_SIZE);
        await window.printerCharacteristic.writeValue(chunk); // Enviar cada pacote
        offset += MAX_CHUNK_SIZE;
    }

}

export {
    connectPrinter,
    printData,
}
