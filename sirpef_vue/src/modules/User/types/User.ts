export default interface User {
  id?: string;
  name: string | null;
  email: string | null;
  cedula: string | null;
  password: string | null;
  role_id: string; 
  oficinas_ids: string[] | null | any;
  eventos_id: string[] | null | any;
  menus_id: string[] | null | any;
}
