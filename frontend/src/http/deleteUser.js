import { api } from "@/lib/axios";

export async function apiDeleteUser(id) {
  try
  {
    const response = await api.delete(`/delete/${id}`);
    return response.data;
  } catch (error)
  {
    console.error("Erro ao deletar usuário:", error);
    throw new Error('Falha ao tentar deletar usuário');
  }
}