export default class StringHelper {

  /**
   * Abrevia o nome para duas palavras.
   * @param {string} fullname - Nome completo.
   * @param {boolean} useSecondName - Usar o segundo nome ao invés do último.
   * @returns {string} - Nome abreviado.
   */
  static shortName(fullname, useSecondName = false) {
    fullname = fullname.trim();
    if (!fullname) return '';
    const names = fullname.split(' ');
    if (names.length === 1) return names[0];
    else return names[0] + ' ' + (useSecondName ? names[1] : names[names.length - 1]);
  }

  /**
   * Obtem duas letras que abreviam com o primeiro e último nome.
   * @param {string} name - Nome completo ou parcial.
   * @returns {string} - Iniciais.
   */
  static nameInitials(name) {
    name = name.trim();
    if (!name) return '';
    const names = name.split(' ');
    if (names.length === 1) return names[0].substr(0,2);
    else return names[0].substr(0,1) + names[names.length - 1].substr(0,1);
  }

  /**
   * Extrai os digitos numericos da string.
   * @param {string} string
   * @returns {string}
   */
  static extractNumbers(string) {
    return string.replace(/\D/g,'');
  }

  /**
   * Valida um CPF brasileiro.
   * @param {string} cpf Numeros do CPF.
   * @returns {boolean}
   */
  static validCpf(cpf) {
    let strCPF = cpf;
    if (typeof strCPF == 'number') strCPF = strCPF.toString();
    strCPF = strCPF.replace(/\D+/g, '');
    if (strCPF.length !== 11) return false;
    let Soma = 0;
    let Resto;
    let i;
    if (strCPF === "00000000000") return false;

    for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto === 10) || (Resto === 11))  Resto = 0;
    if (Resto !== parseInt(strCPF.substring(9, 10)) ) return false;

    Soma = 0;
    for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
    Resto = (Soma * 10) % 11;

    if ((Resto === 10) || (Resto === 11))  Resto = 0;
    return Resto === parseInt(strCPF.substring(10, 11));
  }

  /**
   * Valida um CPNJ brasileiro.
   * @param {string} cnpj Numeros do CPNJ.
   * @returns {boolean}
   */
  static validCnpj(cnpj) {
    if (typeof cnpj == 'number') cnpj = cnpj.toString();
    cnpj = cnpj.replace(/\D+/g, '');
    if (cnpj.length !== 14) return false;

    // Elimina CNPJs invalidos conhecidos
    if (cnpj === "00000000000000" ||
      cnpj === "11111111111111" ||
      cnpj === "22222222222222" ||
      cnpj === "33333333333333" ||
      cnpj === "44444444444444" ||
      cnpj === "55555555555555" ||
      cnpj === "66666666666666" ||
      cnpj === "77777777777777" ||
      cnpj === "88888888888888" ||
      cnpj === "99999999999999")
      return false;

    // Valida DVs
    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0,tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;
    let i;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2) pos = 9;
    }
    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado.toString() !== digitos.charAt(0)) return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
        pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    return resultado.toString() === digitos.charAt(1);
  }

  /**
   * Converte uma string monetaria em formato R$ 1.250,99 para double (1250.99)
   * @param monetaryString
   * @returns {number}
   */
  static monetaryToDouble(monetaryString) {
    if (!monetaryString) return 0;
    return parseFloat(monetaryString.replaceAll('.', '').replaceAll(',', '.').replace(/[^\d.-]/g,''));
  }
}