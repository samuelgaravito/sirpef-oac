import Http from "@/utils/Http";

export default {
  getEmployes(query) {  
    return Http.get(`/api/registro/?${query}`);
  }, 
  deleteVoto (id) {
    return Http.delete(`/api/registro/delete-vote/${id}`);
  }
 }
