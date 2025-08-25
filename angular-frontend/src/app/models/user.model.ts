export interface User {
  EMPID?: number;
  EMPNAME: string;
  EMPPOSITION: string;
  USERNAME: string;
  PASSWRD?: string;
  ACCSTATUS: string;
  EMPSEX: string;
  COMPANY: string;
  DEPARTMENT: string;
  EMPLOYID: string;
  AVELEAVE: number;
}

export interface LoginRequest {
  user_email: string;
  user_pass: string;
}

export interface LoginResponse {
  success: boolean;
  message: string;
  user?: User;
  token?: string;
}