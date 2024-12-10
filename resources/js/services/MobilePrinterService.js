import { ref } from 'vue'

const isConnected = ref(false)
const error = ref(null)
const device = ref(null)

const connectPrinter = async () => {
    try {
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

    } catch (err) {
        error.value = err.message
        isConnected.value = false
    }
}

const printData = async (data) => {
    if (!window.printerCharacteristic) {
        throw new Error('Printer not connected')
    }

    const encoder = new TextEncoder()
    const dataArray = encoder.encode(data)
    await window.printerCharacteristic.writeValue(dataArray)
}

export {
    connectPrinter,
    printData,
}
