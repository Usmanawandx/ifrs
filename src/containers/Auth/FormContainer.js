import React from 'react';
import { KeyboardAvoidingView, Text, Button } from 'components';
import { StyleSheet, View, SafeAreaView } from 'react-native';
import PropTypes from 'prop-types';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  innerContainer: {
    flex: 1,
    backgroundColor: Colors.white,
    paddingVertical: scale(24),
    paddingHorizontal: scale(14),
    borderTopLeftRadius: scale(20),
    borderTopRightRadius: scale(20),
  },
  welcome: {
    marginBottom: scale(24),
  },
  buttonContainer: {
    paddingVertical: scale(14),
  },
});

const FormContainer = ({
  title, subtitle, children, buttonLabel, onSubmit,
}) => (
  <KeyboardAvoidingView contentContainerStyle={styles.container}>
    <View style={styles.innerContainer}>
      <View style={styles.welcome}>
        <Text font="h2" weight="medium">{title}</Text>
        <Text color="gray75">
          {subtitle}
        </Text>
      </View>
      <View style={styles.container}>
        {children}
      </View>
      <SafeAreaView>
        <View style={styles.buttonContainer}>
          <Button
            label={buttonLabel}
            onPress={onSubmit}
          />
        </View>
      </SafeAreaView>
    </View>
  </KeyboardAvoidingView>
);

FormContainer.propTypes = {
  children: PropTypes.node.isRequired,
  title: PropTypes.string.isRequired,
  subtitle: PropTypes.string.isRequired,
  buttonLabel: PropTypes.string.isRequired,
  onSubmit: PropTypes.func.isRequired,
};

export default FormContainer;
