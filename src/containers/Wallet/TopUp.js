import React, { useState } from 'react';
import {
  Container,
  NavBar,
  TextField,
  KeyboardAvoidingView,
  Button,
  Text,
  Divider,
  PaymentSelect,
} from 'components';
import PropTypes from 'prop-types';
import { View, TouchableOpacity, StyleSheet } from 'react-native';
import { scale } from 'react-native-size-matters';
import Colors from 'themes/colors';
import { getProfile } from 'mocks/me';

const styles = StyleSheet.create({
  container: {
    padding: scale(14),
  },
  optionContainer: {
    flexDirection: 'row',
    marginTop: scale(14),
  },
  option: {
    padding: scale(10),
    flex: 1,
    borderWidth: 1,
    borderColor: Colors.gray25,
    borderRadius: scale(60),
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: Colors.gray5,
  },
  space: {
    marginHorizontal: scale(14),
  },
  selected: {
    backgroundColor: Colors.primaryBg,
    borderColor: Colors.primary,
  },
  buttonContainer: {
    paddingVertical: scale(14),
  },
  payment: {
    marginBottom: scale(14),
  },
});

const profile = getProfile();
const values = ['$50', '$100', '$200'];

const TopUp = ({ navigation }) => {
  const [selectedIndex, setSelectedIndex] = useState(null);

  const onSelect = (index) => {
    setSelectedIndex((currentIndex) => {
      if (currentIndex === index) {
        return null;
      }
      return index;
    });
  };

  return (
    <Container backgroundColor="white">
      <NavBar
        title="Top Up"
        onLeftIconPress={() => navigation.goBack()}
      />
      <KeyboardAvoidingView contentContainerStyle={styles.container}>
        <Text color="gray75" weight="medium">Select amount:</Text>
        <View style={styles.optionContainer}>
          {values.map((value, index) => {
            const isSelected = selectedIndex === index;
            return (
              <TouchableOpacity
                style={[
                  styles.option,
                  index === 1 && styles.space,
                  isSelected && styles.selected,
                ]}
                onPress={() => onSelect(index)}
                key={value}
              >
                <Text
                  color={isSelected ? 'primary' : 'gray100'}
                  weight={isSelected ? 'medium' : 'regular'}
                >
                  {value}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>
        <Divider>
          <Text color="gray50">or</Text>
        </Divider>
        <TextField
          label="Enter your preferred amount"
        />
        <Text
          color="gray75"
          weight="medium"
          style={styles.payment}
        >
          Select payment method:
        </Text>
        <PaymentSelect
          options={[
            ...profile.cards,
            {
              title: 'PayPal',
              icon: 'paypal',
              subtitle: 'Redirect to PayPal',
              iconColor: Colors.blue,
            },
            {
              title: 'Online Banking',
              subtitle: 'Internet banking log-in needed',
              icon: 'university',
              iconColor: Colors.tertiary,
            },
          ]}
        />
        <View style={styles.buttonContainer}>
          <Button label="Top Up" />
        </View>
      </KeyboardAvoidingView>
    </Container>
  );
};

TopUp.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default TopUp;
