import React from 'react';
import { StyleSheet, View, TouchableOpacity } from 'react-native';
import {
  Text, Card, GradientIconBg,
} from 'components';
import { scale } from 'react-native-size-matters';
import { getDefaultAddress } from 'mocks/me';
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';
import Colors from 'themes/colors';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  card: {
    marginBottom: scale(14),
  },
  container: {
    padding: scale(14),
    flexDirection: 'row',
    alignItems: 'center',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  content: {
    marginHorizontal: scale(14),
    flex: 1,
  },
});

const AddressCard = ({ navigation }) => {
  const { name, address } = getDefaultAddress();
  return (
    <Card style={styles.card}>
      <TouchableOpacity
        style={styles.container}
        onPress={() => navigation.navigate('AddressBook')}
      >
        <GradientIconBg
          icon="map-marker"
        />
        <View style={styles.content}>
          <Text weight="medium">{name}</Text>
          <Text color="gray75">{address}</Text>
        </View>
        <Icon
          name="chevron-right"
          color={Colors.gray25}
          size={scale(20)}
        />
      </TouchableOpacity>
    </Card>
  );
};

AddressCard.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default AddressCard;
