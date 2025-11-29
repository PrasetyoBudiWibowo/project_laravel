import axios from "axios";

export async function getLevelUser() {
  try {
    const response = await axios.get("/user/level");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Terjadi kesalahan pada server.",
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}

export async function getDataUserRegister() {
  try {
    const response = await axios.get("/user/get-user");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Terjadi kesalahan pada server.",
      });
      return [];
    }
  } catch (error) {
    Swal.fire({
      icon: "error",
      title: "Gagal",
      text: `Terjadi kesalahan ${error.message}.`,
    });
    return [];
  }
}