async function getLevelUser() {
  try {
    const response = await axios.get("/user/level");

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

async function getDataUserRegister()
{
  try {
    const response = await axios.get("/user/get-user");

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

async function getAllDataKaryawan() {
  try {
    const response = await axios.get("/hrd/karyawan");

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

async function checkSession() {
  return await axios.get('/check-session', {
    withCredentials: true
  });
}

async function userByCode(encryptedId) {
  try {
    const response = await axios.get(`/user/detail/${encryptedId}`);

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

async function getAllDataProvinsi() {
    try {
    const response = await axios.get(`/wilayah/get-provinsi`);

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
window.getDataUserRegister = getDataUserRegister;
window.getAllDataKaryawan = getAllDataKaryawan;
window.checkSession = checkSession;
window.userByCode = userByCode;
window.getAllDataProvinsi = getAllDataProvinsi;