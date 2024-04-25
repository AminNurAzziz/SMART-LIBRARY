import { useMutation, useQuery } from 'react-query';
import { useStudentStore } from '../../providers/auth.provider';
import { apiGet, apiPost } from '../axios-client';
import { LoginPayload } from '../../@types/auth/auth-types';
import { AxiosError } from 'axios';
import { jwtDecode } from '../../auth/utils';

interface AuthResponse {
  data: {
    token: string;
  };
}

export const useLogin = () => {
  const { login } = useStudentStore();
  return useMutation(
    {
      mutationFn: (payload: LoginPayload) => apiPost<AuthResponse>('/v1/auth/login', payload),
      onError: (error: AxiosError) => {
        console.log('error', error);
      },
      onSuccess: (data: AuthResponse) => {
        const decodedToken = jwtDecode(data?.data?.token);
        login({
          isAuthenticated: true,
          token: data.data?.token,
          profile: {
            userId: decodedToken?.userId,
            username: decodedToken?.username,
            email: decodedToken?.email,
            role: decodedToken?.role,
            roleId: decodedToken?.roleId,
            nim: decodedToken?.nim,
          },
        });
      },
    });
};

export const useStudentAuth = (query = {}) => {
  return useQuery({
    queryKey: ['student_auth', query],
    queryFn: () => apiGet('student', query),
    enabled: false,
  });
}