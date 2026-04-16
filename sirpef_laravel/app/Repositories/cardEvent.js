import React from 'react';
import { View, Text, TouchableOpacity, StyleSheet} from 'react-native';

const evento = {
    titulo: "Jornada de Ferraries",
    fecha_inicio: "20-08-2002",
    fecha_fin: "20-08-2002",
    descripcion: "Una jornada que entrega ferraries"
}

const EventCard = ({navigation}) => {
  return (
        
       <View style={styles.card}>
       <View style={styles.content}>
          <View style={styles.gridContainer}>
            <View>
              <Text style={styles.label}>Inicio</Text>
              <Text style={styles.boldText}>{evento.fecha_inicio}</Text>
            </View>
            <View style={styles.justifyEnd}>
              <Text style={styles.label}>Final</Text>
              <Text style={styles.boldText}>{evento.fecha_fin}</Text>
            </View>
          </View>
          <Text style={styles.title}>{evento.titulo}</Text>
          {/*<Text style={styles.description}>{evento.descripcion}</Text> */}
          <View style={styles.flexContainer}>
            <TouchableOpacity
              title="entrar"
              onPress={() => navigation.navigate("QR")}
              style={styles.enterButton}
            >
              <Text>Aceptar</Text>
            </TouchableOpacity>
          </View>
        </View>
       </View>
  );
};

const styles = StyleSheet.create({
  card: {
    width: "90%",
    backgroundColor: '#0a254b',
    padding: 6,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 4,
    elevation: 5,
    borderRadius: 10,
    transform: [{ scale: 1 }],
  },
 

  button: {
    padding: 10,
    borderRadius: 10,
    backgroundColor: '#FFFFFF',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 4,
    elevation: 5,
  },

  content: {
    padding: 10,
  },
  gridContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  label: {
    fontSize: 14,
    color: '#AAAAAA',
  },
  boldText: {
    fontSize: 14,
    color: '#FFFFFF',
  },
  justifyEnd: {
    justifyContent: 'flex-end',
  },
  title: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginTop: 20,
  },
  description: {
    fontSize: 16,
    color: '#FFFFFF',
    marginVertical: 10,
  },
  flexContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  enterButton: {
    padding: 10,
    borderRadius: 10,
    backgroundColor: '#34C759',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 4,
    margin: "auto",
    width: '100%',
    marginTop: 20,
    alignItems: "center"
  },
});

export default EventCard;