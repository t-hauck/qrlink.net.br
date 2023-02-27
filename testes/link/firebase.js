////////////////////////
// Configuração Firebase
//
// https://firebase.google.com/docs/web/setup#available-libraries
import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.7/firebase-app.js";

import {
  getFirestore,
  collection,
  getDocs,
  onSnapshot,
  addDoc,
  doc,
  getDoc,
} from "https://www.gstatic.com/firebasejs/9.6.7/firebase-firestore.js";

// Your web app's Firebase configuration
const firebaseConfig = {

  apiKey: "AIzaSyCxNMTGC8l_JY4zIjbE3KJyoljnX9xHddM",
  authDomain: "encurta-qrlink.firebaseapp.com",
  databaseURL: "https://encurta-qrlink-default-rtdb.firebaseio.com",
  projectId: "encurta-qrlink",
  storageBucket: "encurta-qrlink.appspot.com",
  messagingSenderId: "681525674712",
  appId: "1:681525674712:web:d7f0f1227f53a894340dad"
};

// Initialize Firebase
export const app = initializeApp(firebaseConfig);

export const db = getFirestore();

/**
 * Save a New Task in Firestore
 * @param {string} title the title of the Task
 * @param {string} description the description of the Task
 */
export const saveLink = (link_completo, link_curto) =>
  addDoc(collection(db, "link"), { link_completo, link_curto });

export const onGetLinks = (callback) =>
  onSnapshot(collection(db, "link"), callback);

/**
 *
 * @param {string} id Link ID
 */
//export const deleteTask = (id) => deleteDoc(doc(db, "tasks", id));

export const getLink = (id) => getDoc(doc(db, "link", id));

//export const updateTask = (id, newFields) =>
//  updateDoc(doc(db, "tasks", id), newFields);

export const getLinks = () => getDocs(collection(db, "link"));
