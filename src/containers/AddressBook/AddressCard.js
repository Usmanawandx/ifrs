import React from 'react';
import { StyleSheet, View, TouchableOpacity } from 'react-native';
import {
  Card, Text, GradientIconBg, Label,
} from 'components';
import { scale, verticalScale } from 'react-native-size-matters';
import PropTypes from 'prop-types';
import Colors from 'themes/colors';
import capitalize from 'lodash/capitalize';

const styles = StyleSheet.create({
  card: {
    marginBottom: scale(14),
  },
  container: {
    padding: scale(14),
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  content: {
    flex: 1,
    marginLeft: scale(14),
  },
  divider: {
    borderLeftWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.gray25,
    height: verticalScale(20),
    marginHorizontal: scale(14),
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  label: {
    marginLeft: scale(10),
  },
});

const AddressCard = ({ address, navigation }) => {
  const getIconType = () => {
    switch (address.type) {
      case 'school':
        return {
          icon: 'school',
          gradient: ['#ff9966', '#ff5e62'],
        };
      case 'office':
        return {
          icon: 'shopping',
          gradient: ['#7F7FD5', '#86A8E7', '#91EAE4'],
        };
      case 'home':
      default:
        return {
          icon: 'home',
        };
    }
  };

  const { icon, gradient } = getIconType();

  return (
    <Card style={styles.card}>
      <View style={styles.container}>
        <GradientIconBg
          icon={icon}
          gradient={gradient}
        />
        <View style={styles.content}>
          <View style={styles.row}>
            <Text weight="medium">{address.name}</Text>
            {address.isDefault && (
              <Label
                style={styles.label}
                textColor="white"
                backgroundColor="green"
              >
                Default
              </Label>
            )}
            <Label style={styles.label}>{capitalize(address.type)}</Label>
          </View>
          <Text color="gray50">{address.phoneNumber}</Text>
          <Text>{address.address}</Text>
        </View>
        <View style={styles.divider} />
        <TouchableOpacity
          onPress={() => navigation.navigate('EditAddress', {
            initialValues: {
              ...address,
            },
          })}
        >
          <Text color="gray50">Edit</Text>
        </TouchableOpacity>
      </View>
    </Card>
  );
};

AddressCard.propTypes = {
  address: PropTypes.object.isRequired,
  navigation: PropTypes.object.isRequired,
};

export default AddressCard;
