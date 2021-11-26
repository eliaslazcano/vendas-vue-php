export default class JwtHelper {

  constructor(token) {
    this.token = token;
  }

  get payload() {
    if (!this.token) return null;
    const parts = this.token.split('.');
    if (parts.length !== 3) return null;
    return JSON.parse(atob(parts[1]));
  }

  static getPayload(token) {
    const jwt = new JwtHelper(token);
    return jwt.payload;
  }
}
