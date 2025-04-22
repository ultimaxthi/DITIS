import { api } from "@/lib/axios";

export async function apiGetUsers() {
  try {
    const response = await api.get('/users/');
    const data = response.data;

    const extractUsers = (usersArray) =>
      usersArray.map((user) => ({
        id: user.id,
        name: user.name || user.nome || "Usuário sem nome",
        email: user.email || "",
        role: user.role || user.papel || "",
      }));

    if(Array.isArray(data)) {
      return extractUsers(data);
    }

    const possibleArrays = ["users", "data", "items", "records"];
    for (const key of possibleArrays) {
      if (Array.isArray(data[key])) {
          return extractUsers(data[key]);
      }  
    }
    
    
    console.warn('API não retornou um array de usuários');
    return [];
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
    return [];
  }
}