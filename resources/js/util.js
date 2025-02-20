// utils.js - Arquivo unificado para funções utilitárias com aplicação dos princípios SOLID

// ==================
// 1. ClassNames Utility (Responsabilidade: Gerenciar classes CSS)
// ==================
export { default as ClassNamesUtil } from './utils/ClassNames';


// ==================
// 2. State Utility (Responsabilidade: Atualização de estados reativos)
// ==================
export { default as updateValue } from './utils/updateValue';

// ==================
// 3. Error Utility (Responsabilidade: Tratamento e padronização de erros)
// ==================
export { default as ErrorUtil } from './utils/ErrorUtil';

// ==================
// 4. Array e Object Utilities (Responsabilidade: Operações em arrays e objetos)
// ==================
export { default as ArrayUtil } from './utils/ArrayUtil';

export { default as ObjectUtil } from './utils/ObjectUtil';

// ==================
// 5. String Utilities (Responsabilidade: Manipulação e decodificação de strings)
// ==================
export { default as StringUtil } from './utils/StringUtil';

// ==================
// 6. Date Utilities (Responsabilidade: Manipulação e formatação de datas)
// ==================
export { default as DateUtil } from './utils/DateUtil';

// ==================
// 7. Money Utilities (Responsabilidade: Formatação monetária)
// ==================
export { default as MoneyUtil } from './utils/MoneyUtil';

// ==================
// 8. Password Utilities (Responsabilidade: Geração e embaralhamento de senhas)
// ==================
export { default as PasswordUtil } from './utils/PasswordUtil';

// ==================
// 9. Clipboard Utilities (Responsabilidade: Formatação para área de transferência)
// ==================
export { default as ClipboardUtil } from './utils/ClipboardUtil';

// ==================
// 10. Order Utilities (Responsabilidade: Formatação e status de pedidos)
// ==================
export { default as OrderUtil } from './utils/OrderUtil';

// ==================
// 11. Client Utilities (Responsabilidade: Formatação e obtenção de dados do cliente)
// ==================
export { default as ClientUtil } from './utils/ClientUtil';

// ==================
// 12. Google Autocomplete Utility (Responsabilidade: Integração com Google Places)
// ==================
export { default as GoogleAutocompleteUtil } from './utils/GoogleAutocompleteUtil';
