async function getLevelUser() {
  try {
    const response = await axios.get("/get-level-user");

    if (response.data.status === "success") {
      return response.data.data;
    } else {
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: `Terjadi kesalahan pada server.`,
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

window.getLevelUser = getLevelUser;