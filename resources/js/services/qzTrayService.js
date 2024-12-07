import { ref } from "vue";
import qz from "qz-tray";
import { QZ_SECURITY } from "@/config/qz-config";

const qzAvailable = ref(false);
const connected = ref(null);

const certificate = `-----BEGIN CERTIFICATE-----
MIIEBjCCAu6gAwIBAgIJAOJrQsHMj2TaMA0GCSqGSIb3DQEBCwUAMIGXMQswCQYD
VQQGEwJCUjEQMA4GA1UECAwHUGFyYWliYTEPMA0GA1UEBwwGSmVyaWNvMRIwEAYD
VQQKDAlUb2t1bXNlZGUxCzAJBgNVBAsMAlRJMRswGQYDVQQDDBIqLnRva3Vtc2Vk
ZS5jb20uYnIxJzAlBgkqhkiG9w0BCQEWGHR1bGlvZ2FsdmFvLnBiQGdtYWlsLmNv
bTAeFw0xNzA5MjQxNzM3NTZaFw00OTAzMTkxNzM3NTZaMIGXMQswCQYDVQQGEwJC
UjEQMA4GA1UECAwHUGFyYWliYTEPMA0GA1UEBwwGSmVyaWNvMRIwEAYDVQQKDAlU
b2t1bXNlZGUxCzAJBgNVBAsMAlRJMRswGQYDVQQDDBIqLnRva3Vtc2VkZS5jb20u
YnIxJzAlBgkqhkiG9w0BCQEWGHR1bGlvZ2FsdmFvLnBiQGdtYWlsLmNvbTCCASIw
DQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALsZvyHqUsupDlATczH99sPo5dak
s1CwdqrAbBRG24HiTuk51ianCvA4RYacApJsoVQKQQhqEitMCcJ1LMW/sFfCT2aG
bc6FXo8Kpgg383jnmrxcDZGou4qDOOS1Kksbz8CJYlD/UVucayfXRqYRi7DHSDQC
ix5bfiumZFxqPGd3i6N09/nm37Zb9zKpTJFpsY6fw1WPKZFZ6x4UOnZ7NDcNHse/
7qQl4M84KM5RV8c9Bq17C8N+OMMfIRJNpAzUo+KOv/9kvXQl7YffUANB5Br4QWEi
bET+Etac8OeT6Qy31K9gERKTE6olS3HzuLUIY2Jq4Tyja83v0WvU/M8x0zUCAwEA
AaNTMFEwHQYDVR0OBBYEFNQ1pt/TYpH24TPPmBjZXL03KfN3MB8GA1UdIwQYMBaA
FNQ1pt/TYpH24TPPmBjZXL03KfN3MA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcN
AQELBQADggEBABnoCtapmJ4dj6MW9Uy6vTYsXy4RtKqoPTxRsNVaBE0BQgYeocVu
f08vRoXBEMKQVJruY/G8yhKbNYBPmqohgONsvhI5Rcf6ZvS1NqD3OQ8sBiwI2yCg
YBpSu7JcuJuaiXiU9pv90YdUf4WI/jUIOKiAmGQpcFHdexVFW0mb2VfxOooahdBP
elApY3vYRlTtjcmU4JaQ+dt5H+ebjUs4sJsroHfxJuQLxExDdvy/0M264hnqm1ad
iW0Tb2wlgBb7r3xgISxGIFeI7V1tqB90qidrFu8F/pUXwl6c02qr/zwjS5m9QnaP
FEFhTIKoFft56SNdx3U143h/NvnAccxVa0s=
-----END CERTIFICATE-----`;
var privateKey =
    "-----BEGIN PRIVATE KEY-----\n" +
    "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCkd1LEW4i1TqFU\n" +
    "XUG6nzlzmVX4iBuLGF9lB4mxvV+fYCCU7ufESFy3YWGle7hA1MUG3UnVrfxuwsgq\n" +
    "MtJaXZk0Q23YAWwTs3iC/QhC6h0k+MMmqkYTU+Bc1UlLqQzk4JNJI/yiDB5tdT6m\n" +
    "kI9VrhyXZ/AhVRNqDbZE5YIXqaJPYiVwmaghO+FePWxJqU4bSK6Vg7YDFFJ6sGIu\n" +
    "H106ZV2Ot6au5jLZyvkGjc4pPdx9xfZeAHshTCQE1TxQ1iR5UMEGM5ofQZwGVc6Z\n" +
    "1HSvzDt1Dr7VrAoCAPX37Z0Ag3rc4RjxE6NxskiN+s1pEPlvjWv/WowVzERlm7HE\n" +
    "tgLal/nbAgMBAAECggEAEI54lQ7X6NSlFg6bTtO3n2UIzA+7ohmOhOeo221CgpNV\n" +
    "RFj2mQJl3wodH+EgD9q7iPDe/XVZ67aNGv5pwbIZebLuDGg8PpF7KMibO81AqNeo\n" +
    "IazTiB+R/xZznfvDMglPmnXWeWO57m/2oiL8YvY3p6BNgrWDUlJWDoKCQaqQjegc\n" +
    "XYzy9YLr8igFcDiTbThi+mGwWkh8LG6HVGGZ43uIWlGzmFwMJJ+Mdgc0QONkiccI\n" +
    "HnMPZAN9C3tSAcZnTX2eqrqMuWBEQfvU6Zdy+PKWAhAXML5qH3x2YTANXVZkAME5\n" +
    "bYPHgvOR4n5BSTuyNPUvz84F+zgGoNkHQB9n4M290QKBgQC2TRegE/Gf8FUYy7bU\n" +
    "Mgz6wiKJSag9nBCJGh7f1wxF5El3Vv8lnjDtff6Y2J4abbt1a3CG/iBRfIp249rz\n" +
    "o5GBJNFgG3V/wiOJ1x59ReP0/qWcYsjA6rmwkWIvdhMdM+xujzn7l6HclhNDagMg\n" +
    "Z1ae/A/xTrdkUGuT/tio4EwSsQKBgQDm9G2e9OhC5pqtstWj4kt3zp6q7kcQx3iH\n" +
    "4yH8P/aPR8rKZWZ6sDIvyp1IXQkDTXZ50/WkWz7fR7rVXq+ZrwF1w39PnIFVKSDK\n" +
    "xKz7LnqZ+rRJC1vFzmdiq9lDipCY+8Vgpebi5n9JfWY399ZmtragHlzUqWW0/1YF\n" +
    "yXyT69aASwKBgQCE3OLfFCoBuxMKI054kJHNIDgzfq9TV67lfVgLI5waRCsXAxyp\n" +
    "ugVG0ZEArL9t25PIHCnC+Ots+CuiQqaM8yVUzhSayuhz2HY2O8ZI3uso336r34MY\n" +
    "tvnmqc65cIC1w+YJHfHQX87kCay4cUceErKa5HJqGEion8QH9LDLQ82twQKBgQCB\n" +
    "8RpQKgkXwvlaK1lKWMMPSGA7Wc8AIMqu4ds4OqC1orX1RDHxa3sBKqVtlnLAue+j\n" +
    "wd7eNzxbkdcLv7da530RzgmuOCcITBiYHSoaNN9kDQssYcijtWqzuG6IMskCWf2G\n" +
    "UDFkjj0lkvlVGgs2RSzhT9P5DsobmOHEZcXC0BkimwKBgEJV8GwZF/3GPbp7G1h/\n" +
    "VRjrCIvW7+au3xWC0NsA4GP/e3mNPyinBHDjnl1/mz+fK/63WyEIjuJwPLz3cFO+\n" +
    "x9q7tTcUphCte+/hdwgHEFylfQEir5sHiGMFZkz+qdTiLcD6ouquEmPue5yQt/sR\n" +
    "xjoRG8LJ7iVf6dPJ0MUTenan\n" +
    "-----END PRIVATE KEY-----";

const setCertificatePromise = async () => {
    await qz.security.setCertificatePromise(function (resolve, reject) {
        resolve(certificate);
    });

    qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
    await qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            try {
                var pk = KEYUTIL.getKey(privateKey);
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
const checkConnection = async () => {
    try {
        const isConnected = await qz.websocket.isActive();
        if (isConnected) {
            return true;
        } else {
            return false;
        }
    } catch (error) {
        console.error("Erro ao verificar a conexão com QZ Tray:", error);
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
        try {
            const printer = await qz.printers.find(printerName);
            console.log("findPrinter");
            console.log(printer);
            return printer;
        } catch (err) {
            console.error("Error finding printer:", err);
            return null;
        }
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
};
