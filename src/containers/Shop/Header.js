import React from 'react';
import {
  Image, View, StyleSheet,
} from 'react-native';
import PropTypes from 'prop-types';
import {
  Text, VerifiedBadge, Button,
} from 'components';
import { getScreenHeight } from 'utils/size';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';

const styles = StyleSheet.create({
  header: {
    backgroundColor: Colors.white,
    paddingHorizontal: scale(14),
    paddingBottom: scale(14),
  },
  controlContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  imageContainer: {
    height: scale(100),
    width: scale(100),
    borderRadius: scale(50),
    overflow: 'hidden',
    borderWidth: 5,
    borderColor: Colors.white,
    marginTop: scale(-50),
  },
  name: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  space: {
    marginBottom: scale(10),
  },
  banner: {
    height: getScreenHeight() / 4,
  },
  logo: {
    flex: 1,
    width: undefined,
    height: undefined,
  },
  button: {
    flex: 0.5,
  },
});

const Shop = ({ shop }) => (
  <>
    <Image
      source={shop.banner}
      resizeMode="cover"
      style={styles.banner}
    />
    <View style={styles.header}>
      <View style={styles.controlContainer}>
        <View style={styles.imageContainer}>
          <Image
            resizeMode="cover"
            source={shop.image}
            style={styles.logo}
          />
        </View>
        <View style={styles.button}>
          <Button size="tiny" label="Follow" />
        </View>
      </View>
      <View style={styles.name}>
        <Text font="h2" weight="medium">
          {shop.name}
          {' '}
        </Text>
        <VerifiedBadge />
      </View>
      <Text color="gray50" style={styles.space}>{shop.username}</Text>
      <Text style={styles.space}>{shop.tagline}</Text>
      <Text>{`${shop.followers} Followers`}</Text>
    </View>
  </>
);

Shop.propTypes = {
  shop: PropTypes.object.isRequired,
};

export default Shop;
