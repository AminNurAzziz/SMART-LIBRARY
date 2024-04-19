export interface LoginPayload {
  username: string;
  password: string;
}

export interface AuthState {
  user: UserData | null;
  login: (user: UserData) => void;
  logout: () => void;
}

export interface UserData {
  isAuthenticated: boolean;
  token: string;
  profile: {
    userId: string;
    username: string;
    email: string;
    role: string;
    roleId: string;
    nim?: string;
    nip?: string;
  };
}