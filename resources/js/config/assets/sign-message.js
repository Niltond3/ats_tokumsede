import qz from "qz-tray";
/*
 * JavaScript client-side example using jsrsasign
 */

// #########################################################
// #             WARNING   WARNING   WARNING               #
// #########################################################
// #                                                       #
// # This file is intended for demonstration purposes      #
// # only.                                                 #
// #                                                       #
// # It is the SOLE responsibility of YOU, the programmer  #
// # to prevent against unauthorized access to any signing #
// # functions.                                            #
// #                                                       #
// # Organizations that do not protect against un-         #
// # authorized signing will be black-listed to prevent    #
// # software piracy.                                      #
// #                                                       #
// # -QZ Industries, LLC                                   #
// #                                                       #
// #########################################################

/**
 * Depends:
 *     - jsrsasign-latest-all-min.js
 *     - qz-tray.js
 *
 * Steps:
 *
 *     1. Include jsrsasign 10.9.0 into your web page
 *        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/11.1.0/jsrsasign-all-min.js"></script>
 *
 *     2. Update the privateKey below with contents from private-key.pem
 *
 *     3. Include this script into your web page
 *        <script src="path/to/sign-message.js"></script>
 *
 *     4. Remove or comment out any other references to "setSignaturePromise"
 *
 *     5. IMPORTANT: Before deploying to production, copy "jsrsasign-all-min.js"
 *        to the web server.  Don't trust the CDN above to be available.
 */
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

qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
qz.security.setSignaturePromise(function (toSign) {
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
