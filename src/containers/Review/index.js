import React from 'react';
import {
  Container,
  NavBar,
  StarsField,
  Button,
  KeyboardAvoidingView,
  Text,
  TextField,
} from 'components';
import PropTypes from 'prop-types';
import { SafeAreaView, View, StyleSheet } from 'react-native';
import { scale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  container: {
    padding: scale(14),
    flex: 1,
  },
  buttonContainer: {
    paddingVertical: scale(14),
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: scale(7),
  },
  form: {
    flex: 1,
  },
  textarea: {
    marginVertical: scale(14),
  },
});

const Review = ({ navigation }) => (
  <Container backgroundColor="white">
    <NavBar
      title="Add Review"
      onLeftIconPress={() => navigation.goBack()}
    />
    <KeyboardAvoidingView contentContainerStyle={styles.container}>
      <View style={styles.form}>
        <View style={styles.row}>
          <Text>Product</Text>
          <StarsField />
        </View>
        <View style={styles.row}>
          <Text>Delivery</Text>
          <StarsField />
        </View>
        <View style={styles.row}>
          <Text>Communication</Text>
          <StarsField />
        </View>
        <View style={styles.textarea}>
          <TextField
            label="Your review (optional)"
            multiline
            numberOfLines={10}
          />
        </View>
      </View>
      <SafeAreaView>
        <View style={styles.buttonContainer}>
          <Button
            label="Add Review"
            onPress={() => navigation.goBack()}
          />
        </View>
      </SafeAreaView>
    </KeyboardAvoidingView>
  </Container>
);

Review.propTypes = {
  navigation: PropTypes.object.isRequired,
};

export default Review;
