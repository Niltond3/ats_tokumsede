import { ref } from "vue";
import qz from "qz-tray";
import { QZ_SECURITY } from "@/config/qz-config";

const qzAvailable = ref(false);
const connected = ref(null);

const setCertificatePromise = async () => {
    await qz.security.setCertificatePromise(function (resolve, reject) {
        resolve(QZ_SECURITY.certificate);
    });

    qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
    await qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            try {
                var pk = KEYUTIL.getKey(QZ_SECURITY.privateKey);
                var sig = new KJUR.crypto.Signature({ alg: "SHA512withRSA" }); // Use "SHA1withRSA" for QZ Tray 2.0 and older
                sig.init(pk);
                sig.updateString(toSign);
                var hex = sig.sign();
                console.log("DEBUG: \n\n" + stob64(hextorstr(hex)));
                resolve(stob64(hextorstr(hex)));
            } catch (err) {
                console.error(err);
                reject(err);
            }
        };
    });
};
const connectToQZ = async () => {
    try {
        // Tenta se conectar ao QZ Tray
        await qz.websocket.connect();
        // Se a conexão for bem-sucedida
        connected.value = true;
        console.log("Conectado ao QZ Tray");
    } catch (error) {
        connected.value = false;
        // Se ocorrer algum erro, suprimimos qualquer mensagem
        console.error(
            "Erro ao conectar ao QZ Tray, mas vamos ignorar: ",
            error
        );
    }
};

export const qzTrayService = {
    async connect() {
        try {
            await setCertificatePromise();
            await connectToQZ();
            if (!connected.value) {
                console.log(
                    "QZ Tray não está disponível. Verifique a instalação."
                );
                return;
            }
            console.log("certificate");
            return true;
        } catch (err) {
            console.error("Error connecting to QZ Tray:", err);
            return false;
        }
    },

    async listPrinters() {
        try {
            const printers = await qz.printers.find();
            console.log("listPrinters");
            console.log(printers);
            return printers;
        } catch (err) {
            console.error("Error finding printers:", err);
            return false;
        }
    },
    async findPrinter(printerName) {
        const printer = await qz.printers.find(printerName);
        console.log("findPrinter");
        console.log(printer);
        return printer;
    },
    async print(printer, data) {
        try {
            const config = qz.configs.create(printer);
            await qz.print(config, data);
            return true;
        } catch (err) {
            console.error("Error printing:", err);
            return false;
        }
    },
    async checkConnection() {
        try {
            const isConnected = await qz.websocket.isActive();
            if (isConnected) return true;
            return false;
        } catch (error) {
            console.error("Erro ao verificar a conexão com QZ Tray:", error);
        }
    },
};
