import React from 'react';
import { StyleSheet, View } from 'react-native';
import {
  Card, TextField, Button,
} from 'components';
import { scale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  card: {
    padding: scale(14),
    marginBottom: scale(14),
    flexDirection: 'row',
  },
  textField: {
    flex: 1,
    marginRight: scale(14),
  },
  half: {
    flex: 0.5,
  },
});

const PromoCard = () => (
  <Card style={styles.card}>
    <View style={styles.textField}>
      <TextField label="Promo code" hideError />
    </View>
    <View style={styles.half}>
      <Button label="Apply" />
    </View>
  </Card>
);

export default PromoCard;
